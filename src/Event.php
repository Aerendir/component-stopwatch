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

use SerendipityHQ\Component\Stopwatch\Properties\OriginTrait;

/**
 * Represents an Event managed by Stopwatch.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Event
{
    use OriginTrait;

    /** @var string $category */
    private $category;

    /** @var Period[] $started */
    private $started = [];

    /** @var Period[] $periods */
    private $periods = [];

    /**
     * @param string|null $category The event category or null to use the default
     *
     * @throws \InvalidArgumentException When the raw time is not valid
     */
    public function __construct(?string $category = null)
    {
        $this->initializeOrigins();
        $this->category = $category ?? 'default';
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
     * @return Period[] An array of Period instances
     */
    public function getPeriods(): array
    {
        return $this->periods;
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
        $count = count($this->getPeriods());

        return 0 === $count ? $this->getPeriods()[$count - 1]->getTime()->getEndTime() : 0;
    }

    /**
     * Gets the duration of the events (including all periods, both stopped and still measuring).
     *
     * @param bool $includeStarted if the calculation should include also started but not still closed Periods
     *
     * @return float The duration
     */
    public function getDuration(bool $includeStarted = false): float
    {
        $periods = $includeStarted ? $this->includeClonedStarted() : $this->getPeriods();

        $total = 0;

        /** @var Period $period */
        foreach ($periods as $period) {
            $total += $period->getTime()->getDuration();
        }

        return $total;
    }

    /**
     * Of all periods, gets the max memory amount assigned to PHP.
     *
     * Very similar to Event::getMemoryPeak().
     *
     * @param bool $includeStarted if the calculation should include also started but not still closed Periods
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemory(bool $includeStarted = false): int
    {
        $periods = $includeStarted ? $this->includeClonedStarted() : $this->getPeriods();

        $memory = 0;

        foreach ($periods as $period) {
            if ($period->getMemory()->getEndMemory() > $memory) {
                $memory = $period->getMemory()->getEndMemory();
            }
        }

        return $memory;
    }

    /**
     * Of all periods, gets the max amount of memory used by the script.
     *
     * Very similar to Event::getMemoryPeakEmalloc().
     *
     * @param bool $includeStarted if the calculation should include also started but not still closed Periods
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryCurrent(bool $includeStarted = false): int
    {
        $periods = $includeStarted ? $this->includeClonedStarted() : $this->getPeriods();

        $memoryCurrent = 0;
        foreach ($periods as $period) {
            if ($period->getMemory()->getEndMemoryCurrent() > $memoryCurrent) {
                $memoryCurrent = $period->getMemory()->getEndMemoryCurrent();
            }
        }

        return $memoryCurrent;
    }

    /**
     * Of all periods, gets the max peak amount of memory assigned to PHP.
     *
     * @param bool $includeStarted if the calculation should include also started but not still closed Periods
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryPeak(bool $includeStarted = false): int
    {
        $periods = $includeStarted ? $this->includeClonedStarted() : $this->getPeriods();

        $memoryPeak = 0;
        foreach ($periods as $period) {
            if ($period->getMemory()->getEndMemoryPeak() > $memoryPeak) {
                $memoryPeak = $period->getMemory()->getEndMemoryPeak();
            }
        }

        return $memoryPeak;
    }

    /**
     * Of all periods, gets the max amount of memory assigned to PHP and used by emalloc().
     *
     * @param bool $includeStarted if the calculation should include also started but not still closed Periods
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryPeakEmalloc(bool $includeStarted = false): int
    {
        $periods = $includeStarted ? $this->includeClonedStarted() : $this->getPeriods();

        $memoryPeakCurrent = 0;
        foreach ($periods as $period) {
            if ($period->getMemory()->getEndMemoryPeakEmalloc() > $memoryPeakCurrent) {
                $memoryPeakCurrent = $period->getMemory()->getEndMemoryPeakEmalloc();
            }
        }

        return $memoryPeakCurrent;
    }

    /**
     * Stops all non already stopped periods.
     */
    public function ensureStopped(): void
    {
        while (0 !== count($this->started)) {
            $this->stop();
        }
    }

    /**
     * Starts a new event period.
     *
     * @return Event
     *
     * @internal Use the Stopwatch object instead
     */
    public function start(): Event
    {
        $this->started[] = new Period();

        return $this;
    }

    /**
     * Stops the last started event period.
     *
     * @throws \LogicException When stop() is called without a matching call to start()
     *
     * @return Event
     *
     * @internal Use the Stopwatch object instead
     */
    public function stop(): Event
    {
        if (0 === count($this->started)) {
            throw new \LogicException('stop() called but start() has not been called before.');
        }

        /** @var Period $period */
        $period = array_pop($this->started);

        $this->periods[] = $period->stop();

        $this->originTime->stop();
        $this->originMemory->stop();

        return $this;
    }

    /**
     * Stops the current period and then starts a new one.
     *
     * @return Event
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
     * @return bool
     *
     * @internal
     */
    public function isStarted(): bool
    {
        return ! empty($this->started);
    }

    /**
     * @return array
     */
    private function includeClonedStarted(): array
    {
        $periods = $this->getPeriods();

        $stopped = count($periods);
        $left    = count($this->started) - $stopped;

        for ($i = 0; $i < $left; ++$i) {
            $index = $stopped + $i;
            // Clone the Period to not really close it
            $period    = clone $this->started[$index];
            $periods[] = $period->stop();
        }

        return $periods;
    }
}
