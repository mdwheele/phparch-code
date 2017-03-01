<?php

namespace Sample\Workflow;

/**
 * Represents a collection of process definitions
 */
interface ProcessCatalog
{
    /**
     * Add a new Process Definition to the catalog; retrievable by identity
     *
     * @param ProcessDefinition $definition
     */
    public function add(ProcessDefinition $definition);

    /**
     * Retrieve a Process Definition by its identifier
     *
     * @param ProcessDefinitionId $definitionId
     *
     * @return ProcessDefinition
     */
    public function get(ProcessDefinitionId $definitionId);
}
