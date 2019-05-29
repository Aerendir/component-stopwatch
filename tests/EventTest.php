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
class EventTest extends TestCase
{
    private const DELTA = 0.037;

    public function testGetCategory()
    {
        $event = new Event();
        self::assertEquals('default', $event->getCategory());

        $event = new Event('cat');
        self::assertEquals('cat', $event->getCategory());
    }

    public function testGetPeriods()
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

    public function testLap()
    {
        $event = new Event();
        $event->start();
        $event->lap();
        $event->stop();
        self::assertCount(2, $event->getPeriods());
    }

    public function testDuration()
    {
        $event = new Event();
        $event->start();
        usleep(200000);
        $event->stop();
        self::assertLessThan(0.205, $event->getDuration());

        $event = new Event();
        $event->start();
        usleep(100000);
        $event->stop();
        usleep(50000);
        $event->start();
        usleep(100000);
        $event->stop();
        self::assertEqualsWithDelta(0.205, $event->getDuration(), self::DELTA);
    }

    public function testDurationBeforeStop()
    {
        $event = new Event();
        $event->start();
        usleep(200000);
        self::assertEqualsWithDelta(0.0, $event->getDuration(), self::DELTA);

        $event = new Event();
        $event->start();
        usleep(100000);
        $event->stop();
        usleep(50000);
        $event->start();
        usleep(100000);
        self::assertEqualsWithDelta(0.100, $event->getDuration(), self::DELTA);
    }

    public function testDurationBeforeStopIncludingStarted()
    {
        $event = new Event();
        $event->start();
        usleep(200000);
        self::assertEqualsWithDelta(0.205, $event->getDuration(true), self::DELTA);

        $event = new Event();
        $event->start();
        usleep(100000);
        $event->stop();
        usleep(50000);
        $event->start();
        usleep(100000);
        self::assertEqualsWithDelta(0.100, $event->getDuration(), self::DELTA);
    }

    public function testStopWithoutStart()
    {
        $this->expectException(\LogicException::class);
        $event = new Event();
        $event->stop();
    }

    public function testIsStarted()
    {
        $event = new Event();
        $event->start();
        self::assertTrue($event->isStarted());
    }

    public function testIsNotStarted()
    {
        $event = new Event();
        self::assertFalse($event->isStarted());
    }

    public function testEnsureStopped()
    {
        // this also test overlap between two periods
        $event = new Event();
        $event->start();
        usleep(100000);
        $event->start();
        usleep(100000);
        $event->ensureStopped();
        self::assertEqualsWithDelta(0.301, $event->getDuration(), self::DELTA);
    }

    public function testStartTime()
    {
        $event = new Event();
        self::assertLessThanOrEqual(0.5, $event->getStartTime());

        $event = new Event();
        $event->start();
        $event->stop();
        self::assertLessThanOrEqual(microtime(true), $event->getStartTime());
    }
}
