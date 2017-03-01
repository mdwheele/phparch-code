<?php

namespace Sample\Workflow;

trait NodeContractTests
{
    /** @test */
    public function it_has_a_unique_identifier()
    {
        $node = $this->getNodeImplementation();
        $this->assertNotEmpty($node->getId());
    }

    /** @test */
    public function it_can_be_connected_to_other_nodes()
    {
        $node = $this->getNodeImplementation();
        $anotherNode = $this->getOtherNodeImplementation();

        $node->flowInto($anotherNode);

        $this->assertNotEmpty($anotherNode->getInputArcs());
        $this->assertNotEmpty($node->getOutputArcs());
    }
}
