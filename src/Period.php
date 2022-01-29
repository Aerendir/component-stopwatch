<?php

declare(strict_types=1);

/*
 * This file is part of the Serendipity HQ Stopwatch Component.
 *
 * Copyright (c) Adamo Aerendir Crespi <aerendir@serendipityhq.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
final class Period
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
        $this->time   = $time   ?? new Time();
        $this->memory = $memory ?? new Memory();
    }

    public function getTime(): Time
    {
        return $this->time;
    }

    public function getMemory(): Memory
    {
        return $this->memory;
    }

    /**
     * Stops the Period.
     *
     * @internal use Stopwatch::stop() or Stopwatch::stopSection()
     */
    public function stop(): self
    {
        $this->getTime()->stop();
        $this->getMemory()->stop();

        return $this;
    }

    public function isStopped(): bool
    {
        return $this->getTime()->isStopped() && $this->getMemory()->isStopped();
    }
}
