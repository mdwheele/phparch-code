<?php

namespace Sample\Workflow\ReadModel\Simulation;

use Sample\Support\AttributeStore;

class Step
{
    public $heading;
    public $summary;
    public $graph;

    /**
     * @var AttributeStore
     */
    public $attributes;

    public function __construct($heading, $summary, $graph, AttributeStore $attributes)
    {
        $this->summary = $summary;
        $this->graph = $graph;
        $this->attributes = json_encode($attributes->all(), JSON_PRETTY_PRINT);
        $this->heading = $heading;
    }
}