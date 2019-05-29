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

namespace SerendipityHQ\Component\Stopwatch\Properties;

/**
 * Used by Section and Event to save the origin time and memory.
 */
class Origin
{
    /** @var float $originTime */
    private $originTime;

    /** @var Memory $originMemory */
    private $originMemory;

    /** @var int $originMemoryCurrent Of the memory assigned to PHP, the amount of memory currently consumed by the script */
    private $originMemoryCurrent;

    /** @var int $originMemoryPeak The max amount of memory assigned to PHP */
    private $originMemoryPeak;

    /** @var int $originMemoryPeakEmalloc The max amount of memory assigned to PHP and used by emalloc() */
    private $originMemoryPeakEmalloc;

    /**
     * Sets the current time and memories.
     */
    public function __construct()
    {
        $this->originTime              = microtime(true);
        $memories                      = Memory::measure();
        $this->originMemory            = $memories['memory_get_usage_true'];
        $this->originMemoryCurrent     = $memories['memory_get_usage'];
        $this->originMemoryPeak        = $memories['memory_get_peak_usage_true'];
        $this->originMemoryPeakEmalloc = $memories['memory_get_peak_usage'];
    }

    /**
     * @return float
     */
    public function getOriginTime(): float
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
     * Of the memory assigned to PHP, gets the amount of memory currently used by the script.
     *
     * @return int
     */
    public function getOriginMemoryCurrent(): int
    {
        return $this->originMemoryCurrent;
    }

    /**
     * Gets the max amount of memory assigned to PHP.
     *
     * @return int
     */
    public function getOriginMemoryPeak(): int
    {
        return $this->originMemoryPeak;
    }

    /**
     * Gets the max amount of memory used by emalloc().
     *
     * @return int
     */
    public function getOriginMemoryPeakEmalloc(): int
    {
        return $this->originMemoryPeakEmalloc;
    }
}
