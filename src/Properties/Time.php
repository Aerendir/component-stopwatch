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

use LogicException;

/**
 * Manages timings.
 *
 * @author Adamo Crespi <hello@aerendir.me>
 */
final class Time
{
    /** @var float $startTime */
    private $startTime;

    /** @var float|null $endTime */
    private $endTime;

    /**
     * Sets the start time.
     */
    public function __construct()
    {
        $this->startTime = \microtime(true);
    }

    /**
     * @return float
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }

    /**
     * @throws LogicException If the end time is retrieved before the Period is stopped
     *
     * @return float
     */
    public function getEndTime(): float
    {
        if (null === $this->endTime) {
            throw new LogicException('Not yet stopped: you cannot get the end time until you stop.');
        }

        return $this->endTime;
    }

    /**
     * The difference between the stop time and the start time = the duration.
     *
     * @throws LogicException if the method is called before the Period to which it belongs to is stopped
     *
     * @return float
     */
    public function getDuration(): float
    {
        if (null === $this->endTime) {
            throw new LogicException('Not yet stopped: you cannot get duration until you stop.');
        }

        return $this->endTime - $this->startTime;
    }

    /**
     * Sets the stop time.
     *
     * @internal use Stopwatch::stop() or Stopwatch::stopSection()
     */
    public function stop(): void
    {
        $this->endTime = \microtime(true);
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return null !== $this->endTime;
    }
}
