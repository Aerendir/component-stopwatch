<?php

namespace SerendipityHQ\Component\Stopwatch\Properties;

/**
 * Used by Section and Event to save the origin time and memory.
 */
trait OriginTrait
{
    /** @var Time $originTime */
    private $originTime;

    /** @var Memory $originMemory */
    private $originMemory;

    /**
     * @return Time
     */
    public function getOriginTime(): Time
    {
        return $this->originTime;
    }

    /**
     * @return Memory
     */
    public function getOriginMemory(): Memory
    {
        return $this->originMemory;
    }

    /**
     * Creates the Time and Memory objects.
     */
    private function initializeOrigins():void
    {
        $this->originTime   = new Time();
        $this->originMemory = new Memory();
    }
}
