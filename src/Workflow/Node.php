<?php

namespace Sample\Workflow;

use Sample\Support\Uuid;
use Illuminate\Support\Collection;

abstract class Node
{
    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var Collection
     */
    protected $outputArcs;

    /**
     * @var Collection
     */
    protected $inputArcs;

    public function __construct()
    {
        $this->id = Uuid::make();
        $this->inputArcs = new Collection();
        $this->outputArcs = new Collection();
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    public function flowInto(Node... $nodes)
    {
        foreach ($nodes as $node) {
            if ($this instanceof Condition) {
                $arc = new Arc($this, $node);
            } else {
                $arc = new Arc($node, $this);
            }

            $this->addOutputArc($arc);
            $node->addInputArc($arc);
        }
    }

    public function flowOutOf(Node... $nodes)
    {
        foreach ($nodes as $node) {
            if ($this instanceof Condition) {
                $arc = new Arc($this, $node);
            } else {
                $arc = new Arc($node, $this);
            }

            $this->addInputArc($arc);
            $node->addOutputArc($arc);
        }
    }

    protected function addInputArc(Arc $arc)
    {
        if (in_array($arc, $this->inputArcs->all())) {
            return;
        }

        $this->inputArcs[] = $arc;
    }

    protected function addOutputArc(Arc $arc)
    {
        if (in_array($arc, $this->outputArcs->all())) {
            return;
        }

        $this->outputArcs[] = $arc;
    }

    /**
     * @return Arc[]
     */
    public function getInputArcs()
    {
        return $this->inputArcs->all();
    }

    /**
     * @return Arc[]
     */
    public function getOutputArcs()
    {
        return $this->outputArcs->all();
    }
}
