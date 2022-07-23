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

namespace SerendipityHQ\Component\Stopwatch\Properties;

/**
 * Manages timings.
 *
 * @author Adamo Crespi <hello@aerendir.me>
 */
final class Memory
{
    /** @var string */
    private const MEMORY_GET_USAGE_TRUE = 'memory_get_usage_true';

    /** @var string */
    private const MEMORY_GET_USAGE = 'memory_get_usage';

    /** @var string */
    private const MEMORY_GET_PEAK_USAGE_TRUE = 'memory_get_peak_usage_true';

    /** @var string */
    private const MEMORY_GET_PEAK_USAGE = 'memory_get_peak_usage';

    /** @var int $startMemory The amount of memory assigned to PHP */
    private $startMemory;

    /** @var int $startMemoryCurrent Of the memory assigned to PHP, the amount of memory currently consumed by the script */
    private $startMemoryCurrent;

    /** @var int $startMemoryPeak The max amount of memory assigned to PHP */
    private $startMemoryPeak;

    /** @var int $startMemoryPeakEmalloc The max amount of memory assigned to PHP and used by emalloc() */
    private $startMemoryPeakEmalloc;

    /** @var int|null $endMemory The amount of memory assigned to PHP */
    private $endMemory;

    /** @var int|null $endMemoryCurrent Of the memory assigned to PHP, the amount of memory currently consumed by the script */
    private $endMemoryCurrent;

    /** @var int|null $endMemoryPeak The max amount of memory assigned to PHP */
    private $endMemoryPeak;

    /** @var int|null $endMemoryPeakEmalloc The max amount of memory assigned to PHP and used by emalloc() */
    private $endMemoryPeakEmalloc;

    /**
     * Sets the start time.
     */
    public function __construct()
    {
        $memories                     = self::measure();
        $this->startMemory            = $memories[self::MEMORY_GET_USAGE_TRUE];
        $this->startMemoryCurrent     = $memories[self::MEMORY_GET_USAGE];
        $this->startMemoryPeak        = $memories[self::MEMORY_GET_PEAK_USAGE_TRUE];
        $this->startMemoryPeakEmalloc = $memories[self::MEMORY_GET_PEAK_USAGE];
    }

    public static function measure(): array
    {
        return [
            self::MEMORY_GET_USAGE_TRUE      => \memory_get_usage(true),
            self::MEMORY_GET_USAGE           => \memory_get_usage(),
            self::MEMORY_GET_PEAK_USAGE_TRUE => \memory_get_peak_usage(true),
            self::MEMORY_GET_PEAK_USAGE      => \memory_get_peak_usage(),
        ];
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
     */
    public function getStartMemoryCurrent(): int
    {
        return $this->startMemoryCurrent;
    }

    /**
     * Gets the max amount of memory assigned to PHP.
     */
    public function getStartMemoryPeak(): int
    {
        return $this->startMemoryPeak;
    }

    /**
     * Gets the max amount of memory used by emalloc().
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
        if (null === $this->endMemory) {
            throw new \LogicException("The Period is not yet stopped: you cannot call this method if you don't stop it.");
        }

        return $this->endMemory;
    }

    /**
     * Of the memory assigned to PHP, gets the amount of memory currently used by the script.
     */
    public function getEndMemoryCurrent(): int
    {
        if (null === $this->endMemoryCurrent) {
            throw new \LogicException("The Period is not yet stopped: you cannot call this method if you don't stop it.");
        }

        return $this->endMemoryCurrent;
    }

    /**
     * Gets the max amount of memory assigned to PHP.
     */
    public function getEndMemoryPeak(): int
    {
        if (null === $this->endMemoryPeak) {
            throw new \LogicException("The Period is not yet stopped: you cannot call this method if you don't stop it.");
        }

        return $this->endMemoryPeak;
    }

    /**
     * Gets the max amount of memory used by emalloc().
     */
    public function getEndMemoryPeakEmalloc(): int
    {
        if (null === $this->endMemoryPeakEmalloc) {
            throw new \LogicException("The Period is not yet stopped: you cannot call this method if you don't stop it.");
        }

        return $this->endMemoryPeakEmalloc;
    }

    /**
     * Sets the stop time.
     *
     * @internal use Stopwatch::stop() or Stopwatch::stopSection()
     */
    public function stop(): void
    {
        $memories                   = self::measure();
        $this->endMemory            = $memories[self::MEMORY_GET_USAGE_TRUE];
        $this->endMemoryCurrent     = $memories[self::MEMORY_GET_USAGE];
        $this->endMemoryPeak        = $memories[self::MEMORY_GET_PEAK_USAGE_TRUE];
        $this->endMemoryPeakEmalloc = $memories[self::MEMORY_GET_PEAK_USAGE];
    }

    public function isStopped(): bool
    {
        return null !== $this->endMemory || null !== $this->endMemoryCurrent || null !== $this->endMemoryPeak || null !== $this->endMemoryPeakEmalloc;
    }
}
