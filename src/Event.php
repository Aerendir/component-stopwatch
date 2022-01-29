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

use InvalidArgumentException;
use LogicException;
use SerendipityHQ\Component\Stopwatch\Properties\Origin;

/**
 * Represents an Event managed by Stopwatch.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
final class Event
{
    /** @var Origin $origin */
    private $origin;

    /** @var string $category */
    private $category;

    /** @var Period[] $started */
    private $started = [];

    /** @var Period[] $periods */
    private $periods = [];

    /**
     * @param string|null $category The event category or null to use the default
     *
     * @throws InvalidArgumentException When the raw time is not valid
     */
    public function __construct(?string $category = null)
    {
        $this->origin   = new Origin();
        $this->category = $category ?? 'default';
    }

    public function getOrigin(): Origin
    {
        return $this->origin;
    }

    /**
     * Gets the category.
     *
     * @return string The category
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Gets all event periods.
     *
     * @param bool $includeStillMeasuring if the calculation should include also started but not still closed Periods
     *
     * @return Period[] An array of Period instances
     */
    public function getPeriods(bool $includeStillMeasuring = false): array
    {
        if (false === $includeStillMeasuring) {
            return $this->periods;
        }

        $periods = $this->periods;

        $stopped = \count($periods);
        $left    = \count($this->started) - $stopped;

        for ($i = 0; $i < $left; ++$i) {
            $index  = $stopped + $i;
            $time   = clone $this->started[$index]->getTime();
            $memory = clone $this->started[$index]->getMemory();
            // Clone the Period to not really close it
            $period    = new Period($time, $memory);
            $periods[] = $period->stop();
        }

        return $periods;
    }

    /**
     * Gets the relative time of the start of the first period.
     *
     * @return float The start time of the very first Period
     */
    public function getStartTime(): float
    {
        return isset($this->getPeriods()[0]) ? $this->getPeriods()[0]->getTime()->getStartTime() : 0;
    }

    /**
     * Gets the relative time of the end of the last period.
     *
     * @return float|int The time (in milliseconds)
     */
    public function getEndTime()
    {
        $count = \count($this->getPeriods());

        return 0 === $count ? $this->getPeriods()[$count - 1]->getTime()->getEndTime() : 0;
    }

    /**
     * Gets the duration of the events (including all periods, both stopped and still measuring).
     *
     * @param bool $includeStillMeasuring if the calculation should include also started but not still closed Periods
     *
     * @return float The duration
     */
    public function getDuration(bool $includeStillMeasuring = false): float
    {
        $total = (float) 0;

        /** @var Period $period */
        foreach ($this->getPeriods($includeStillMeasuring) as $period) {
            $total += $period->getTime()->getDuration();
        }

        return $total;
    }

    /**
     * Of all periods, gets the max memory amount assigned to PHP.
     *
     * Very similar to Event::getMemoryPeak().
     *
     * @param bool $includeStillMeasuring if the calculation should include also started but not still closed Periods
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemory(bool $includeStillMeasuring = false): int
    {
        $memory = 0;

        foreach ($this->getPeriods($includeStillMeasuring) as $period) {
            if ($period->getMemory()->getEndMemory() > $memory) {
                $memory = $period->getMemory()->getEndMemory();
            }
        }

        return $memory;
    }

    /**
     * Get the current consumed memory of the last period.
     *
     * If the period is stopped, returns its end current memory,
     * while returns its start current memory if it is still measuring.
     *
     * Very similar to Event::getMemoryPeak().
     *
     * @param bool $includeStillMeasuring if the calculation should include also started but not still closed Periods
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryCurrent(bool $includeStillMeasuring = false): int
    {
        $periods = $includeStillMeasuring ? $this->started : $this->getPeriods();
        $count   = \count($periods);
        // We don't use end() to not modify the internal pointer of the array
        $lastPeriod = $periods[$count - 1];

        return $lastPeriod->getMemory()->isStopped() ? $lastPeriod->getMemory()->getEndMemoryCurrent() : $lastPeriod->getMemory()->getStartMemoryCurrent();
    }

    /**
     * Stops all non already stopped periods.
     */
    public function ensureStopped(): void
    {
        while ([] !== $this->started) {
            $this->stop();
        }
    }

    /**
     * Starts a new event period.
     *
     * @internal Use the Stopwatch object instead
     */
    public function start(): self
    {
        $this->started[] = new Period();

        return $this;
    }

    /**
     * Stops the last started event period.
     *
     * @throws LogicException When stop() is called without a matching call to start()
     *
     * @internal Use the Stopwatch object instead
     */
    public function stop(): self
    {
        if ([] === $this->started) {
            throw new LogicException('stop() called but start() has not been called before.');
        }

        /** @var Period $period */
        $period = \array_pop($this->started);

        $this->periods[] = $period->stop();

        return $this;
    }

    /**
     * Stops the current period and then starts a new one.
     *
     * @throws LogicException
     *
     * @internal Use the Stopwatch object instead
     */
    public function lap(): Event
    {
        return $this->stop()->start();
    }

    /**
     * Checks if the event was started.
     *
     * @internal
     */
    public function isStarted(): bool
    {
        return ! empty($this->started);
    }
}
