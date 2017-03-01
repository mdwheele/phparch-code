<?php

namespace Sample\Workflow;

use PHPUnit\Framework\TestCase;
use Sample\Support\AttributeStore;
use Sample\Support\Uuid;
use Sample\Workflow\Events\WorkflowCompleted;
use Sample\Workflow\Events\WorkflowStarted;
use Sample\Workflow\Events\WorkflowTaskEnabled;
use Sample\Workflow\Events\WorkflowTaskFired;
use Sample\Workflow\Exception\WorkflowExecutionException;
use Sample\Workflow\Factories\ProcessDefinitionFactory;
use Sample\Workflow\Triggers\Trigger;

class WorkflowTest extends TestCase
{

    /** @test */
    public function it_can_be_started()
    {
        $definition = ProcessDefinitionFactory::basic();

        $case = Workflow::start($definition);

        $events = $case->getUncommittedEvents();

        $this->assertCount(4, $events);
        $this->assertEventStream($events, [
            WorkflowStarted::class,
            WorkflowTaskEnabled::class,
            WorkflowTaskFired::class,
            WorkflowCompleted::class
        ]);
        $this->assertNotEmpty($case->getId());
    }

    /** @test */
    public function it_fires_all_enabled_tasks_when_ticked()
    {
        $definition = ProcessDefinitionFactory::basic();

        $case = Workflow::start($definition);

        $case->tick();
        $events = $case->getUncommittedEvents();

        $this->assertCount(4, $events);
        $this->assertEventStream($events, [
            WorkflowStarted::class,
            WorkflowTaskEnabled::class,
            WorkflowTaskFired::class,
            WorkflowCompleted::class,
        ]);
    }

    /** @test */
    public function it_continuously_fires_tasks_until_no_more_are_enabled()
    {
        $definition = ProcessDefinitionFactory::sequential();

        $case = Workflow::start($definition);

        $case->tick();
        $events = $case->getUncommittedEvents();

        $this->assertCount(6, $events);
        $this->assertEventStream($events, [
            WorkflowStarted::class,
            WorkflowTaskEnabled::class,
            WorkflowTaskFired::class,
            WorkflowTaskEnabled::class,
            WorkflowTaskFired::class,
            WorkflowCompleted::class,
        ]);
    }

    /** @test */
    public function it_is_sourced_from_events()
    {
        $case = $this->makeHalfProcessedWorkflow();

        $case->tick();
        $events = $case->getUncommittedEvents();

        $this->assertCount(2, $events);
        $this->assertEventStream($events, [
            WorkflowTaskFired::class,
            WorkflowCompleted::class,
        ]);
    }

    /** @test */
    public function it_doesnt_progress_if_trigger_is_not_satisfied()
    {
        $definition = ProcessDefinitionFactory::neverTriggered();

        $case = Workflow::start($definition);

        $case->tick();
        $case->tick();
        $case->tick();

        $events = $case->getUncommittedEvents();

        $this->assertCount(2, $events);
        $this->assertEventStream($events, [
            WorkflowStarted::class,
            WorkflowTaskEnabled::class
        ]);
    }

    /** @test */
    public function does_not_allow_workflow_to_complete_if_other_conditions_than_sink_are_marked()
    {
        $definition = ProcessDefinitionFactory::tokenLeftover();

        $this->expectException(WorkflowExecutionException::class);
        $this->expectExceptionMessage('Workflow completed while other tasks were enabled.');

        $case = Workflow::start($definition);
    }
    
    /**
     * @return Workflow
     */
    private function makeHalfProcessedWorkflow()
    {
        $caseNumber = Uuid::make();
        $definition = ProcessDefinitionFactory::sequential();

        $marking = new Marking();
        $marking->mark($definition->getSource()
            ->getOutputArcs()[0]->getTask()
            ->getOutputArcs()[0]->getCondition());

        $task = $definition->getSource()->getOutputArcs()[0]->getTask();

        $case = new Workflow();
        $case->initializeState([
            new WorkflowStarted($caseNumber, $definition, $marking, new AttributeStore()),
            new WorkflowTaskEnabled($caseNumber, $task->getId(), Trigger::AUTOMATIC),
            new WorkflowTaskFired($caseNumber, $task->getId(), $marking),
        ]);

        return $case;
    }

    private function assertEventStream(array $events, array $eventTypes)
    {
        $this->assertTrue(count($events) === count($eventTypes), 'Number of expected events did not match actual.');
        
        foreach ($events as $i => $event) {
            $this->assertSame(get_class($event), $eventTypes[$i], 'Either the order of events is wrong or events are missing according to expectations');
        }
    }
}
