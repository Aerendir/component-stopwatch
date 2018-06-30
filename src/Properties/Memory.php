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

namespace SerendipityHQ\Component\Stopwatch\Properties;

/**
 * Manages timings.
 *
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Memory
{
    /** @var int $startMemory The amount of memory assigned to PHP */
    private $startMemory;

    /** @var int $startMemoryCurrent Of the memory assigned to PHP, the amount of memory currently consumed by the script */
    private $startMemoryCurrent;

    /** @var int $startMemoryPeak The max amount of memory assigned to PHP */
    private $startMemoryPeak;

    /** @var int $startMemoryPeakEmalloc The max amount of memory assigned to PHP and used by emalloc() */
    private $startMemoryPeakEmalloc;

    /** @var int $endMemory The amount of memory assigned to PHP */
    private $endMemory;

    /** @var int $endMemoryCurrent Of the memory assigned to PHP, the amount of memory currently consumed by the script */
    private $endMemoryCurrent;

    /** @var int $endMemoryPeak The max amount of memory assigned to PHP */
    private $endMemoryPeak;

    /** @var int $endMemoryPeakEmalloc The max amount of memory assigned to PHP and used by emalloc() */
    private $endMemoryPeakEmalloc;

    /**
     * Sets the start time.
     */
    public function __construct()
    {
        $this->startMemory            = memory_get_usage(true);
        $this->startMemoryCurrent     = memory_get_usage();
        $this->startMemoryPeak        = memory_get_peak_usage(true);
        $this->startMemoryPeakEmalloc = memory_get_peak_usage();
    }

    /**
     * Gets the memory assigned to PHP.
     *
     * @return int The memory usage (in bytes)
     */
    public function getStartMemory(): int
    {
        return $this->startMemory;
    }

    /**
     * Of the memory assigned to PHP, gets the amount of memory currently used by the script.
     *
     * @return int
     */
    public function getStartMemoryCurrent(): int
    {
        return $this->startMemoryCurrent;
    }

    /**
     * Gets the max amount of memory assigned to PHP.
     *
     * @return int
     */
    public function getStartMemoryPeak(): int
    {
        return $this->startMemoryPeak;
    }

    /**
     * Gets the max amount of memory used by emalloc().
     *
     * @return int
     */
    public function getStartMemoryPeakEmalloc(): int
    {
        return $this->startMemoryPeakEmalloc;
    }

    /**
     * Gets the memory assigned to PHP.
     *
     * @return int The memory usage (in bytes)
     */
    public function getEndMemory(): int
    {
        return $this->endMemory;
    }

    /**
     * Of the memory assigned to PHP, gets the amount of memory currently used by the script.
     *
     * @return int
     */
    public function getEndMemoryCurrent(): int
    {
        return $this->endMemoryCurrent;
    }

    /**
     * Gets the max amount of memory assigned to PHP.
     *
     * @return int
     */
    public function getEndMemoryPeak(): int
    {
        return $this->endMemoryPeak;
    }

    /**
     * Gets the max amount of memory used by emalloc().
     *
     * @return int
     */
    public function getEndMemoryPeakEmalloc(): int
    {
        return $this->endMemoryPeakEmalloc;
    }

    /**
     * Sets the stop time.
     *
     * @internal use Stopwatch::stop() or Stopwatch::stopSection()
     */
    public function stop(): void
    {
        $this->endMemory            = memory_get_usage(true);
        $this->endMemoryCurrent     = memory_get_usage();
        $this->endMemoryPeak        = memory_get_peak_usage(true);
        $this->endMemoryPeakEmalloc = memory_get_peak_usage();
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return null !== $this->endMemory || null !== $this->endMemoryCurrent || null !== $this->endMemoryPeak || null !== $this->endMemoryPeakEmalloc;
    }
}
