<?php

namespace Sample\Support;

abstract class Projector
{
    public function handle($event)
    {
        $method = $this->getApplyMethod($event);

        if (! method_exists($this, $method)) {
            return;
        }

        $this->$method($event);
    }

    private function getApplyMethod($event)
    {
        return 'apply' . class_basename($event);
    }
}