<?php

declare(strict_types=1);

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
     *
     * Time and Memory objects are passed when the Event has to
     * calculate some measurement including not still stopped Periods.
     *
     * @param Time|null   $time
     * @param Memory|null $memory
     */
    public function __construct(Time $time = null, Memory $memory = null)
    {
        $this->time   = $time ?? new Time();
        $this->memory = $memory ?? new Memory();
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
}
