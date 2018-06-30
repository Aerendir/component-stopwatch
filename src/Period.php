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
 * Represents an Period for an Event.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Period
{
    /** @var float|int $start */
    private $start;

    /** @var float|int $end */
    private $end;

    /** @var int $memory The amount of memory assigned to PHP */
    private $memory;

    /** @var int $memoryCurrent Of the memory assigned to PHP, the amount of memory currently consumed by the script */
    private $memoryCurrent;

    /** @var int $memoryPeak The max amount of memory assigned to PHP */
    private $memoryPeak;

    /** @var int $memoryPeakEmalloc The max amount of memory assigned to PHP and used by emalloc() */
    private $memoryPeakEmalloc;

    /**
     * @param float $start The relative time of the start of the period (in milliseconds)
     * @param float $end   The relative time of the end of the period (in milliseconds)
     */
    public function __construct(float $start, float $end)
    {
        $this->start             = $start;
        $this->end               = $end;
        $this->memory            = memory_get_usage(true);
        $this->memoryCurrent     = memory_get_usage();
        $this->memoryPeak        = memory_get_peak_usage(true);
        $this->memoryPeakEmalloc = memory_get_peak_usage();
    }

    /**
     * Gets the relative time of the start of the period.
     *
     * @return float The time (in milliseconds)
     */
    public function getStartTime(): float
    {
        return $this->start;
    }

    /**
     * Gets the relative time of the end of the period.
     *
     * @return float The time (in milliseconds)
     */
    public function getEndTime(): float
    {
        return $this->end;
    }

    /**
     * Gets the time spent in this period.
     *
     * @return float The period duration (in milliseconds)
     */
    public function getDuration(): float
    {
        return $this->end - $this->start;
    }

    /**
     * Gets the memory assigned to PHP.
     *
     * @return int The memory usage (in bytes)
     */
    public function getMemory(): int
    {
        return $this->memory;
    }

    /**
     * Of the memory assigned to PHP, gets the amount of memory currently used by the script.
     *
     * @return int
     */
    public function getMemoryCurrent(): int
    {
        return $this->memoryCurrent;
    }

    /**
     * Gets the max amount of memory assigned to PHP.
     *
     * @return int
     */
    public function getMemoryPeak(): int
    {
        return $this->memoryPeak;
    }

    /**
     * Gets the max amount of memory used by emalloc().
     *
     * @return int
     */
    public function getMemoryPeakEmalloc(): int
    {
        return $this->memoryPeakEmalloc;
    }
}
