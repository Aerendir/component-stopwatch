<?php

/*
 *
 * This file is part of the Serendipity HQ Stopwatch Component.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 * (c) Adamo Crespi <hello@aerendir.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with the Symfony Framework.
 */

namespace SerendipityHQ\Component\Stopwatch;

use SerendipityHQ\Component\Stopwatch\Properties\Memory;
use SerendipityHQ\Component\Stopwatch\Properties\Time;

/**
 * Represents an Period for an Event.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Period
{
    /** @var Time $time */
    private $time;

    /** @var Memory $memory */
    private $memory;

    /**
     * Initializes the Period.
     */
    public function __construct()
    {
        $this->time   = new Time();
        $this->memory = new Memory();
    }

    /**
     * @return Time
     */
    public function getTime(): Time
    {
        return $this->time;
    }

    /**
     * @return Memory
     */
    public function getMemory(): Memory
    {
        return $this->memory;
    }

    /**
     * Stops the Period.
     *
     * @return Period
     *
     * @internal use Stopwatch::stop() or Stopwatch::stopSection()
     */
    public function stop(): Period
    {
        $this->getTime()->stop();
        $this->getMemory()->stop();

        return $this;
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return $this->getTime()->isStopped() && $this->getMemory()->isStopped();
    }

    /**
     * @return Period
     */
    public function __clone()
    {
        // Save current objects
        $oldTime   = $this->getTime();
        $oldMemory = $this->getMemory();

        // Assign cloned object to this Period
        $this->memory = clone $this->getMemory();
        $this->time   = clone $this->getTime();

        // Clone this period (with already cloned time and memory)
        $period = clone $this;

        // Reassign old time and memory
        $this->memory = $oldMemory;
        $this->time   = $oldTime;

        // Return the cloned Period
        return $period;
    }
}
