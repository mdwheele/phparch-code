<?php

namespace Sample\Controllers;

use Sample\Application;
use Sample\Workflow\Factories\ProcessDefinitionFactory;
use Sample\Workflow\ReadModel\Simulation\Simulation;
use Sample\Workflow\ReadModel\Simulation\SimulationProjector;
use Sample\Workflow\Workflow;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WorkflowController
{

    public function showBasic(Application $app)
    {
        $definition = ProcessDefinitionFactory::basic();

        $case = Workflow::start($definition);

        $events = $case->getUncommittedEvents();

        // Events would be normally be stored in an EventStore and then dispatched to
        // an bus where projectors are subscribed to receive them.

        $projector = new SimulationProjector(
            $simulation = new Simulation()
        );

        foreach ($events as $event) {
            $projector->handle($event);
        }

        return $app->render('basic.twig', compact('simulation'));
    }

    public function showUserBasic(Application $app)
    {
        $definition = ProcessDefinitionFactory::userTriggeredBasic();
        $firstTask = $definition->getTasks()[0];

        $case = Workflow::start($definition);

        // Our input triggers only care that there is input. However, it
        // is reasonable to actually test / validate user input as part of
        // the user trigger (e.g. We could conditionally route based on message)
        $case->input($firstTask->getId(), ['message' => 'Hello, World!']);

        $events = $case->getUncommittedEvents();

        // Events would be normally be stored in an EventStore and then dispatched to
        // an bus where projectors are subscribed to receive them.

        $projector = new SimulationProjector(
            $simulation = new Simulation()
        );

        foreach ($events as $event) {
            $projector->handle($event);
        }

        return $app->render('basic.twig', compact('simulation'));
    }

    public function showUserComplex(Application $app)
    {
        $definition = ProcessDefinitionFactory::userTriggeredComplex();
        $firstUserTriggeredTask = $definition->getTasks()[1];
        $secondUserTriggeredTask = $definition->getTasks()[3];

        $case = Workflow::start($definition);
        $case->input($firstUserTriggeredTask->getId());
        $case->input($secondUserTriggeredTask->getId());

        $events = $case->getUncommittedEvents();

        // Events would be normally be stored in an EventStore and then dispatched to
        // an bus where projectors are subscribed to receive them.

        $projector = new SimulationProjector(
            $simulation = new Simulation()
        );

        foreach ($events as $event) {
            $projector->handle($event);
        }

        return $app->render('basic.twig', compact('simulation'));
    }
}