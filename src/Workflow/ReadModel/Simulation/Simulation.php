<?php

namespace Sample\Workflow\ReadModel\Simulation;

class Simulation
{
    private $steps = [];

    public function add(Step $step)
    {
        $this->steps[] = $step;
    }

    /**
     * @return Step[]
     */
    public function getSteps()
    {
        return $this->steps;
    }

    public function asArray()
    {
        return array_map(function($step) {
            return [
                'heading' => $step->heading,
                'summary' => $step->summary,
                'img' => $step->img,
                'graph' => $step->graph,
                'attributes' => json_decode($step->attributes)
            ];
        }, $this->getSteps());
    }

    public function asJson()
    {
        return json_encode($this->asArray());
    }
}