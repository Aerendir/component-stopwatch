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

/**
 * Represents an Event managed by Stopwatch.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
class StopwatchEvent
{
    /**
     * @var Period[]
     */
    private $periods = [];

    /**
     * @var float
     */
    private $origin;

    /**
     * @var string
     */
    private $category;

    /**
     * @var bool
     */
    private $morePrecision;

    /**
     * @var float[]
     */
    private $started = [];

    /**
     * @param float       $origin        The origin time in milliseconds
     * @param string|null $category      The event category or null to use the default
     * @param bool        $morePrecision If true, time is stored as float to keep the original microsecond precision
     *
     * @throws \InvalidArgumentException When the raw time is not valid
     */
    public function __construct(float $origin, string $category = null, bool $morePrecision = false)
    {
        $this->origin        = $this->formatTime($origin);
        $this->category      = $category ?? 'default';
        $this->morePrecision = $morePrecision;
    }

    /**
     * Gets the category.
     *
     * @return string The category
     */
    public function getCategory():string
    {
        return $this->category;
    }

    /**
     * Gets the origin.
     *
     * @return float The origin in milliseconds
     */
    public function getOrigin():float
    {
        return $this->origin;
    }

    /**
     * Checks if the event was started.
     *
     * @return bool
     */
    public function isStarted():bool
    {
        return ! empty($this->started);
    }

    /**
     * Gets all event periods.
     *
     * @return Period[] An array of Period instances
     */
    public function getPeriods():array
    {
        return $this->periods;
    }

    /**
     * Gets the relative time of the start of the first period.
     *
     * @return float|int The time (in milliseconds)
     */
    public function getStartTime()
    {
        return isset($this->periods[0]) ? $this->periods[0]->getStartTime() : 0;
    }

    /**
     * Gets the relative time of the end of the last period.
     *
     * @return float|int The time (in milliseconds)
     */
    public function getEndTime()
    {
        $count = count($this->periods);

        return $count ? $this->periods[$count - 1]->getEndTime() : 0;
    }

    /**
     * Gets the duration of the events (including all periods).
     *
     * @return float|int The duration (in milliseconds)
     */
    public function getDuration()
    {
        $periods = $this->periods;
        $stopped = count($periods);
        $left    = count($this->started) - $stopped;

        for ($i = 0; $i < $left; ++$i) {
            $index     = $stopped + $i;
            $periods[] = new Period($this->started[$index], $this->getNow(), $this->morePrecision);
        }

        $total = 0;
        foreach ($periods as $period) {
            $total += $period->getDuration();
        }

        return $total;
    }

    /**
     * Of all periods, gets the max memory amount assigned to PHP.
     *
     * Very similar to StopwatchEvent::getMemoryPeak().
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemory():int
    {
        $memory = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemory() > $memory) {
                $memory = $period->getMemory();
            }
        }

        return $memory;
    }

    /**
     * Of all periods, gets the max amount of memory used by the script.
     *
     * Very similar to StopwatchEvent::getMemoryPeakEmalloc().
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryCurrent():int
    {
        $memoryCurrent = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemoryCurrent() > $memoryCurrent) {
                $memoryCurrent = $period->getMemoryCurrent();
            }
        }

        return $memoryCurrent;
    }

    /**
     * Of all periods, gets the max amount of memory assigned to PHP.
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryPeak():int
    {
        $memoryPeak = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemoryPeak() > $memoryPeak) {
                $memoryPeak = $period->getMemoryPeak();
            }
        }

        return $memoryPeak;
    }

    /**
     * Of all periods, gets the max amount of memory assigned to PHP and used by emalloc().
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryPeakEmalloc():int
    {
        $memoryPeakCurrent = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemoryPeakEmalloc() > $memoryPeakCurrent) {
                $memoryPeakCurrent = $period->getMemoryPeakEmalloc();
            }
        }

        return $memoryPeakCurrent;
    }

    /**
     * Starts a new event period.
     *
     * @return StopwatchEvent
     * @internal
     */
    public function start():StopwatchEvent
    {
        $this->started[] = $this->getNow();

        return $this;
    }

    /**
     * Stops the last started event period.
     *
     * @throws \LogicException When stop() is called without a matching call to start()
     *
     * @return StopwatchEvent
     *              @internal
     */
    public function stop():StopwatchEvent
    {
        if ( ! count($this->started)) {
            throw new \LogicException('stop() called but start() has not been called before.');
        }

        $this->periods[] = new Period(array_pop($this->started), $this->getNow(), $this->morePrecision);

        return $this;
    }

    /**
     * Stops the current period and then starts a new one.
     *
     * @return StopwatchEvent
     *              @internal
     */
    public function lap():StopwatchEvent
    {
        return $this->stop()->start();
    }

    /**
     * Stops all non already stopped periods.
     * @internal
     */
    public function ensureStopped():void
    {
        while (count($this->started)) {
            $this->stop();
        }
    }

    /**
     * Return the current time relative to origin.
     *
     * @return float Time in ms
     */
    protected function getNow():float
    {
        return $this->formatTime(microtime(true) * 1000 - $this->origin);
    }

    /**
     * Formats a time.
     *
     * @param float|int $time A raw time
     *
     * @throws \InvalidArgumentException When the raw time is not valid
     *
     * @return float The formatted time
     */
    private function formatTime($time):float
    {
        if ( ! is_numeric($time)) {
            throw new \InvalidArgumentException('The time must be a numerical value');
        }

        return round($time, 1);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s: %.2F MiB - %d ms', $this->getCategory(), $this->getMemory() / 1024 / 1024, $this->getDuration());
    }
}
