<?php

namespace Sample\Workflow\Factories;

use Sample\Workflow\Condition;
use Sample\Workflow\ProcessDefinition;
use Sample\Workflow\Task;
use Sample\Workflow\Triggers\Never;

class ProcessDefinitionFactory
{
    /**
     * The simplest process definition, a single-task with the
     * only input condition being the source and the only output condition
     * being the sink.
     *
     * @return ProcessDefinition
     */
    public static function basic()
    {
        $definition = new ProcessDefinition();

        $definition
            ->addCondition($c1 = new Condition)
            ->addTask($c1, $t = new Task, $c2 = new Condition)
            ->setSource($c1)
            ->setSink($c2);

        return $definition;
    }

    /**
     * A sequential process definition with two tasks.
     *
     * @return ProcessDefinition
     */
    public static function sequential()
    {
        $definition = new ProcessDefinition();

        $c1 = new Condition();
        $c2 = new Condition();
        $c3 = new Condition();
        $t1 = new Task();
        $t2 = new Task();

        $definition
            ->addCondition($c1)
            ->addTask($c1, $t1, $c2)
            ->addTask($c2, $t2, $c3)
            ->setSource($c1)
            ->setSink($c3);

        return $definition;
    }

    /**
     * A process definition that will always result in a marking where a
     * condition other than sink is marked at the same time the sink is.
     *
     * @return ProcessDefinition
     */
    public static function tokenLeftover()
    {
        $definition = new ProcessDefinition();

        $c1 = new Condition();
        $c2 = new Condition();
        $c3 = new Condition();
        $t1 = new Task(new Never());
        $t2 = new Task();

        $definition
            ->addCondition($c1)
            ->addTask($c1, $t2, $c3)
            ->addOutputCondition($t2, $c2)
            ->addTask($c2, $t1, $c3)
            ->setSource($c1)
            ->setSink($c3);

        return $definition;
    }

    public static function orSplit()
    {
        $definition = new ProcessDefinition();

        $c1 = new Condition();
        $c2 = new Condition();
        $c3 = new Condition();
        $c4 = new Condition();
        $t1 = Task::automatic();
        $t2 = Task::automatic();
        $t3 = Task::automatic();
        $t4 = Task::automatic();

        $definition
            ->addCondition($c1)
            ->addTask($c1, $t1, $c2)
            ->addTask($c1, $t2, $c3)
            ->addTask($c2, $t3, $c4)
            ->addTask($c3, $t4, $c4)
            ->setSource($c1)
            ->setSink($c4);

        return $definition;
    }

    public static function parallel()
    {
        $c1 = new Condition();
        $c2 = new Condition();
        $c3 = new Condition();
        $c4 = new Condition();
        $c5 = new Condition();
        $c6 = new Condition();
        $t1 = Task::automatic();
        $t2 = Task::automatic();
        $t3 = Task::automatic();
        $t4 = Task::automatic();

        $definition = new ProcessDefinition();
        $definition
            ->addCondition($c1)
            ->addTask($c1, $t1, $c2)
            ->addOutputCondition($t1, $c3)
            ->addTask($c2, $t2, $c4)
            ->addTask($c3, $t3, $c5)
            ->addTask($c4, $t4, $c6)
            ->addTask($c5, $t4 ,$c6)
            ->setSource($c1)
            ->setSink($c6);

        return $definition;
    }

    /**
     * Returns a process with a single task who's trigger will never
     * be satisfied.
     *
     * This is used to verify that multiple subsequent calls to `tick` on
     * a Workflow will not fire duplicate TaskEnabled events. It will be
     * common to find workflows where tasks are enabled but triggers are
     * yet-to-be satisfied.
     *
     * @return ProcessDefinition
     */
    public static function neverTriggered()
    {
        $definition = new ProcessDefinition();

        $definition
            ->addCondition($c1 = new Condition())
            ->addTask($c1, $t = new Task(new Never()), $c2 = new Condition)
            ->setSource($c1)
            ->setSink($c2);

        return $definition;
    }
}