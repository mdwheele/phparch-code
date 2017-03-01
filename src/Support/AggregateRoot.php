<?php

namespace Sample\Support;

abstract class AggregateRoot
{

    /**
     * @var array a stream of
     */
    private $uncommittedEvents = [];

    /**
     * @return string
     */
    abstract public function getId();

    /**
     * This method is used if an implementing aggregate is
     * constructed via null constructor. The client is responsible
     * for instantiation and calling of this method
     *
     * @param $events
     */
    public function initializeState($events)
    {
        foreach ($events as $event) {
            $this->handle($event);
        }
    }

    public function getUncommittedEvents()
    {
        $events = $this->uncommittedEvents;
        $this->uncommittedEvents = [];
        return $events;
    }

    /**
     * Used by classes implementing Entity to apply custom
     * domain events to  state. The event is handled and if no
     * exception occurs, added to an array of uncommitted events.
     *
     * @param $event
     */
    protected function apply($event)
    {
        $this->handle($event);

        $this->uncommittedEvents[] = $event;
    }

    /**
     * Handles a single event by calling a matching method responsible
     * for processing that event and updating relevant entity state.
     *
     * SomethingHappened -> applySomethingHappened(SomethingHappened $e)
     *
     * @param $event
     */
    protected function handle($event)
    {
        $method = $this->getApplyMethod($event);

        if (! method_exists($this, $method)) {
            return;
        }

        $this->$method($event);
    }

    private function getApplyMethod($event)
    {
        return 'apply' . basename(str_replace('\\', '/', get_class($event)));
    }
}