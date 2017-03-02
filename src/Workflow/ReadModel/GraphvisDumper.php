<?php

namespace Sample\Workflow\ReadModel;

use Sample\Workflow\Marking;
use Sample\Workflow\ProcessDefinition;

/**
 * This class is used to export node graphs as DOT language graph
 * specifications. Optionally, this class can render DOT scripts
 * as PNG files and return contents to be used in <img> `src` attribute
 *
 * @codeCoverageIgnore
 */
class GraphvisDumper
{

    private $defaults = [
        'graph' => ['ratio' => 'compress', 'rankdir' => 'LR'],
        'node' => ['fontsize' => 9, 'fontname' => 'Arial', 'color' => '#333333', 'fixedsize' => true, 'width' => 1],
        'edge' => ['fontsize' => 9, 'fontname' => 'Arial', 'color' => '#333333', 'arrowhead' => 'normal', 'arrowsize' => 0.5],
    ];

    /**
     * Dump DOT script from a beginning node.
     *
     * The algorithm visits every reachable node by output arcs. It marks
     * each node that it visits to not render parts of the graph twice.
     *
     * @param ProcessDefinition $definition
     * @param Marking           $marking
     *
     * @return string
     */
    public function dump(ProcessDefinition $definition, Marking $marking)
    {
        $options = $this->defaults;

        return $this->startDot($options)
            .$this->addConditions($this->findConditions($definition, $marking))
            .$this->addTasks($this->findTasks($definition))
            .$this->addArcs($this->findArcs($definition))
            .$this->endDot();
    }

    public function dumpArray(ProcessDefinition $definition, Marking $marking)
    {
        $output = [
            'nodes' => [],
            'edges' => [],
        ];

        foreach ($definition->getConditions() as $condition) {
            $node = [
                'id' => $condition->getId()->toString(),
                'label' => $this->dotize($condition->getId()->toString()),
                'marked' => false,
                'type' => 'condition'
            ];

            if ($definition->getSource() == $condition) {
                $node['type'] = 'source';
            }

            if ($marking->has($condition)) {
                $node['marked'] = true;
            }

            if ($definition->getSink() == $condition) {
                $node['type'] = 'sink';
            }

            $output['nodes'][] = $node;
        }

        foreach ($definition->getTasks() as $task) {
            $output['nodes'][] = [
                'id' => $task->getId()->toString(),
                'label' => $this->dotize($task->getId()->toString()),
                'type' => 'task',
                'trigger' => $task->getTriggerType()
            ];
        }

        $output['edges'] = $this->findArcs($definition, $marking);

        return $output;
    }

    /**
     * Return output that is inline image data in the format `data:image/png`
     * usable in the HTML5 <img> `src` attribute.
     *
     * Requires that the `graphvis` package be installed on the operating
     * system.
     *
     * @param string $dotScriptContents
     *
     * @return string
     */
    public function createImageSrc($dotScriptContents)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'graphviz');
        if ($tmp === false) {
            throw new \UnexpectedValueException('Cannot get temporary file handle for graphviz script');
        }

        if (file_put_contents($tmp, $dotScriptContents, LOCK_EX) === false) {
            throw new \UnexpectedValueException('Unable to write graphviz script to temporary file');
        }

        system("dot -T png {$tmp} -o {$tmp}.png");
        $data = file_get_contents("{$tmp}.png");

        unlink($tmp);
        unlink("{$tmp}.png");

        return sprintf("data:image/png;base64,%s", base64_encode($data));
    }

    private function startDot($options)
    {
        return sprintf(
            "digraph workflow {\n  %s\n  node [%s];\n  edge [%s];\n\n",
            $this->options($options['graph']),
            $this->options($options['node']),
            $this->options($options['edge'])
        );
    }

    private function findConditions(ProcessDefinition $definition, Marking $marking)
    {
        $conditions = [];

        foreach ($definition->getConditions() as $condition) {
            $attributes = [];

            if ($definition->getSource() == $condition) {
                $attributes['style'] = 'filled';
                $attributes['fillcolor'] = '#FFCCCC';
                $attributes['label'] = 'source';
            }

            if ($marking->has($condition)) {
                $attributes['color'] = '#CC0000';
                $attributes['shape'] = 'doublecircle';
            }

            if ($definition->getSink() == $condition) {
                $attributes['style'] = 'filled';
                $attributes['fillcolor'] = '#FFCCCC';
                $attributes['label'] = 'sink';
            }

            $conditions[$condition->getId()->toString()] = [
                'attributes' => $attributes,
            ];
        }

        return $conditions;
    }

    private function findTasks(ProcessDefinition $definition)
    {
        $tasks = [];

        foreach ($definition->getTasks() as $task) {
            $tasks[$task->getId()->toString()] = [
                'attributes' => ['shape' => 'box', 'regular' => true],
            ];
        }

        return $tasks;
    }

    private function findArcs(ProcessDefinition $definition)
    {
        $arcs = [];

        foreach ($definition->getTasks() as $task) {
            foreach ($task->getInputArcs() as $inputArc) {
                $arcs[] = [
                    'from' => $inputArc->getCondition()->getId()->toString(),
                    'to' => $task->getId()->toString(),
                ];
            }

            foreach ($task->getOutputArcs() as $outputArc) {
                $arcs[] = [
                    'from' => $task->getId()->toString(),
                    'to' => $outputArc->getCondition()->getId()->toString(),
                ];
            }
        }

        return $arcs;
    }

    private function addConditions(array $conditions)
    {
        $code = '';

        foreach ($conditions as $id => $condition) {
            $code .= sprintf(
                "  \"%s\" [label=\"%s\", shape=\"circle\"%s];\n",
                $id,
                $this->dotize($id),
                $this->attributes($condition['attributes'])
            );
        }

        return $code;
    }

    private function addTasks(array $tasks)
    {
        $code = '';

        foreach ($tasks as $id => $task) {
            $code .= sprintf(
                "  \"%s\" [label=\"%s\", shape=\"box\"%s];\n",
                $id,
                $this->dotize($id),
                $this->attributes($task['attributes'])
            );
        }

        return $code;
    }

    private function addArcs(array $arcs)
    {
        $code = '';

        foreach ($arcs as $arc) {
            $code .= sprintf(
                "  \"%s\" -> \"%s\" [style=\"solid\"];\n",
                $arc['from'],
                $arc['to']
            );
        }

        return $code;
    }

    private function endDot()
    {
        return "}\n";
    }

    private function options($array)
    {
        $code = [];
        foreach ($array as $k => $v) {
            $code[] = sprintf('%s="%s"', $k, $v);
        }
        return implode(' ', $code);
    }

    private function attributes($attributes)
    {
        $code = [];

        foreach ($attributes as $k => $v) {
            $code[] = sprintf('%s="%s"', $k, $v);
        }

        return $code ? ', ' . implode(', ', $code) : '';
    }

    private function dotize($id)
    {
        return substr($id, 0, 8);
    }
}
