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

namespace SerendipityHQ\Component\Stopwatch\Utils;

use SerendipityHQ\Component\Stopwatch\Event;
use SerendipityHQ\Component\Stopwatch\Period;

/**
 * Helper to extract memory data from Sections and Events.
 *
 * @author Adamo Crespi <hello@aerendir.me>
 */
class MemoryCalc
{
    /** @var Period[] */
    private $periods;

    /**
     * @param Event $event
     * @param bool  $includeStillMeasuring if the calculation should include also started but not still closed Periods
     */
    public function setEvent(Event $event, bool $includeStillMeasuring = false)
    {
        $this->periods = $event->getPeriods($includeStillMeasuring);
    }

    /**
     * Of all periods, gets the max memory amount assigned to PHP recorded by the Period when stopped.
     *
     * Very similar to Event::getMemoryPeak().
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemory(): int
    {
        $memory = 0;

        foreach ($this->periods as $period) {
            if ($period->getMemory()->getEndMemory() > $memory) {
                $memory = $period->getMemory()->getEndMemory();
            }
        }

        return $memory;
    }

    /**
     * Of all periods, gets the max amount of memory used by the script recorded by the Period when stopped.
     *
     * Very similar to Event::getMemoryPeakEmalloc().
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemoryCurrent(): int
    {
        $memoryCurrent = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemory()->getEndMemoryCurrent() > $memoryCurrent) {
                $memoryCurrent = $period->getMemory()->getEndMemoryCurrent();
            }
        }

        return $memoryCurrent;
    }

    /**
     * Of all periods, gets the max peak amount of memory assigned to PHP recorded by the Period when stopped.
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemoryPeak(): int
    {
        $memoryPeak = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemory()->getEndMemoryPeak() > $memoryPeak) {
                $memoryPeak = $period->getMemory()->getEndMemoryPeak();
            }
        }

        return $memoryPeak;
    }

    /**
     * Of all periods, gets the max amount of memory assigned to PHP and used by emalloc() recorded by the Period when stopped.
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemoryPeakEmalloc(): int
    {
        $memoryPeakCurrent = 0;
        foreach ($this->periods as $period) {
            if ($period->getMemory()->getEndMemoryPeakEmalloc() > $memoryPeakCurrent) {
                $memoryPeakCurrent = $period->getMemory()->getEndMemoryPeakEmalloc();
            }
        }

        return $memoryPeakCurrent;
    }

    /**
     * Of the last Period, gets the max memory amount assigned to PHP recorded by the Period when stopped.
     *
     * Very similar to Event::getMemoryPeak().
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemory(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemory();
    }

    /**
     * Of the last Period, gets the max amount of memory used by the script recorded by the Period when stopped.
     *
     * Very similar to Event::getMemoryPeakEmalloc().
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryCurrent(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryCurrent();
    }

    /**
     * Of the last Period, gets the max peak amount of memory assigned to PHP recorded by the Period when stopped.
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryPeak(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryPeak();
    }

    /**
     * Of the last Period, gets the max amount of memory assigned to PHP and used by emalloc() recorded by the Period when stopped.
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemoryPeakEmalloc(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryPeakEmalloc();
    }

    /**
     * Of the last Period, returns the difference between end and start values of memory current.
     *
     * @return int
     */
    public function getMemoryDiff(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemory() - $this->getLastPeriod()->getMemory()->getStartMemory();
    }

    /**
     * Of the last Period, returns the difference between end and start values of memory current.
     *
     * @return int
     */
    public function getMemoryCurrentDiff(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryCurrent() - $this->getLastPeriod()->getMemory()->getStartMemoryCurrent();
    }

    /**
     * Of the last Period, returns the difference between end and start values of memory peak.
     *
     * @return int
     */
    public function getMemoryPeakDiff(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryPeak() - $this->getLastPeriod()->getMemory()->getStartMemoryPeak();
    }

    /**
     * Of the last Period, returns the difference between end and start values of memory peak emalloc.
     *
     * @return int
     */
    public function getMemoryPeakEmallocDiff(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryPeakEmalloc() - $this->getLastPeriod()->getMemory()->getStartMemoryPeakEmalloc();
    }

    /**
     * Returns the last recorded Period.
     *
     * @return Period
     */
    private function getLastPeriod(): Period
    {
        return end($this->periods);
    }
}
