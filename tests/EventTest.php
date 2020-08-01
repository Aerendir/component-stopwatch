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

namespace SerendipityHQ\Component\Stopwatch\Tests;

use PHPUnit\Framework\TestCase;
use SerendipityHQ\Component\Stopwatch\Event;

/**
 * EventTest.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @group time-sensitive
 */
final class EventTest extends TestCase
{
    /** @var float */
    private const DELTA = 0.037;

    public function testGetCategory(): void
    {
        $event = new Event();
        self::assertEquals('default', $event->getCategory());

        $event = new Event('cat');
        self::assertEquals('cat', $event->getCategory());
    }

    public function testGetPeriods(): void
    {
        $event = new Event();
        self::assertEquals([], $event->getPeriods());

        $event = new Event();
        $event->start();
        $event->stop();
        self::assertCount(1, $event->getPeriods());

        $event = new Event();
        $event->start();
        $event->stop();
        $event->start();
        $event->stop();
        self::assertCount(2, $event->getPeriods());
    }

    public function testLap(): void
    {
        $event = new Event();
        $event->start();
        $event->lap();
        $event->stop();
        self::assertCount(2, $event->getPeriods());
    }

    public function testDuration(): void
    {
        $event = new Event();
        $event->start();
        \usleep(200000);
        $event->stop();
        self::assertLessThan(0.205, $event->getDuration());

        $event = new Event();
        $event->start();
        \usleep(100000);
        $event->stop();
        \usleep(50000);
        $event->start();
        \usleep(100000);
        $event->stop();
        self::assertEqualsWithDelta(0.205, $event->getDuration(), self::DELTA);
    }

    public function testDurationBeforeStop(): void
    {
        $event = new Event();
        $event->start();
        \usleep(200000);
        self::assertEqualsWithDelta(0.0, $event->getDuration(), self::DELTA);

        $event = new Event();
        $event->start();
        \usleep(100000);
        $event->stop();
        \usleep(50000);
        $event->start();
        \usleep(100000);
        self::assertEqualsWithDelta(0.100, $event->getDuration(), self::DELTA);
    }

    public function testDurationBeforeStopIncludingStarted(): void
    {
        $event = new Event();
        $event->start();
        \usleep(200000);
        self::assertEqualsWithDelta(0.205, $event->getDuration(true), self::DELTA);

        $event = new Event();
        $event->start();
        \usleep(100000);
        $event->stop();
        \usleep(50000);
        $event->start();
        \usleep(100000);
        self::assertEqualsWithDelta(0.100, $event->getDuration(), self::DELTA);
    }

    public function testStopWithoutStart(): void
    {
        $this->expectException(\LogicException::class);
        $event = new Event();
        $event->stop();
    }

    public function testIsStarted(): void
    {
        $event = new Event();
        $event->start();
        self::assertTrue($event->isStarted());
    }

    public function testIsNotStarted(): void
    {
        $event = new Event();
        self::assertFalse($event->isStarted());
    }

    public function testEnsureStopped(): void
    {
        // this also test overlap between two periods
        $event = new Event();
        $event->start();
        \usleep(100000);
        $event->start();
        \usleep(100000);
        $event->ensureStopped();
        self::assertEqualsWithDelta(0.301, $event->getDuration(), self::DELTA);
    }

    public function testStartTime(): void
    {
        $event = new Event();
        self::assertLessThanOrEqual(0.5, $event->getStartTime());

        $event = new Event();
        $event->start();
        $event->stop();
        self::assertLessThanOrEqual(\microtime(true), $event->getStartTime());
    }
}
