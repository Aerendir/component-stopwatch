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

namespace SerendipityHQ\Component\Stopwatch\Utils;

use LogicException;
use SerendipityHQ\Component\Stopwatch\Event;
use SerendipityHQ\Component\Stopwatch\Period;

/**
 * Helper to extract memory data from Sections and Events.
 *
 * @author Adamo Crespi <hello@aerendir.me>
 */
class MemoryCalc
{
    /** @var Period[]|null */
    private $periods;

    /**
     * @param Event $event
     * @param bool  $includeStillMeasuring if the calculation should include also started but not still closed Periods
     */
    public function setEvent(Event $event, bool $includeStillMeasuring = false): void
    {
        $this->periods = $event->getPeriods($includeStillMeasuring);
    }

    /**
     * Of all periods, gets the max memory amount assigned to PHP recorded by the Period when stopped.
     *
     * Very similar to Event::getMemoryPeak().
     *
     * @throws LogicException if the Event to calc is not passed
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemory(): int
    {
        $memory = 0;

        foreach ($this->getPeriods() as $period) {
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
     * @throws LogicException if the Event to calc is not passed
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemoryCurrent(): int
    {
        $memoryCurrent = 0;
        foreach ($this->getPeriods() as $period) {
            if ($period->getMemory()->getEndMemoryCurrent() > $memoryCurrent) {
                $memoryCurrent = $period->getMemory()->getEndMemoryCurrent();
            }
        }

        return $memoryCurrent;
    }

    /**
     * Of all periods, gets the max peak amount of memory assigned to PHP recorded by the Period when stopped.
     *
     * @throws LogicException if the Event to calc is not passed
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemoryPeak(): int
    {
        $memoryPeak = 0;
        foreach ($this->getPeriods() as $period) {
            if ($period->getMemory()->getEndMemoryPeak() > $memoryPeak) {
                $memoryPeak = $period->getMemory()->getEndMemoryPeak();
            }
        }

        return $memoryPeak;
    }

    /**
     * Of all periods, gets the max amount of memory assigned to PHP and used by emalloc() recorded by the Period when stopped.
     *
     * @throws LogicException if the Event to calc is not passed
     *
     * @return int The memory usage (in bytes)
     */
    public function getAbsoluteEndMemoryPeakEmalloc(): int
    {
        $memoryPeakCurrent = 0;
        foreach ($this->getPeriods() as $period) {
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
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
     * @throws LogicException if the Event to calc is not passed
     *
     * @return int
     */
    public function getMemoryPeakEmallocDiff(): int
    {
        return $this->getLastPeriod()->getMemory()->getEndMemoryPeakEmalloc() - $this->getLastPeriod()->getMemory()->getStartMemoryPeakEmalloc();
    }

    /**
     * @throws LogicException if the Event to calc is not passed
     *
     * @return Period[]
     */
    private function getPeriods(): array
    {
        if (null === $this->periods || empty($this->periods)) {
            throw new LogicException("There is no period set. This means you didn't passed any event. Before using this class, please set an Event using MemoryCalc::setEvent().");
        }

        return $this->periods;
    }

    /**
     * Returns the last recorded Period.
     *
     * @throws LogicException if the Event to calc is not passed
     *
     * @return Period
     */
    private function getLastPeriod(): Period
    {
        $count = count($this->getPeriods());

        // We don't use end() to not modify the internal pointer of the array
        return $this->getPeriods()[$count - 1];
    }
}
