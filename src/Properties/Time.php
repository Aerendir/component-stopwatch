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
class Time
{
    /** @var float $startTime */
    private $startTime;

    /** @var float $endTime */
    private $endTime;

    /**
     * Sets the start time.
     */
    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * @return float
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }

    /**
     * @return float
     */
    public function getEndTime(): float
    {
        return $this->endTime;
    }

    /**
     * The difference between the stop time and the start time = the duration.
     *
     * @return float
     */
    public function getDuration(): float
    {
        if (false === $this->isStopped()) {
            throw new \LogicException('Not yet stopped: you cannot get duration until you stop.');
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
        $this->endTime = microtime(true);
    }

    /**
     * @return bool
     */
    public function isStopped(): bool
    {
        return null !== $this->endTime;
    }
}
