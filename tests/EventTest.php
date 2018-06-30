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
    const DELTA = 0.037;

    public function testGetOrigin()
    {
        $event = new Event(12);
        self::assertEquals(12, $event->getOrigin());
    }

    public function testGetCategory()
    {
        $event = new Event(microtime(true));
        self::assertEquals('default', $event->getCategory());

        $event = new Event(microtime(true), 'cat');
        self::assertEquals('cat', $event->getCategory());
    }

    public function testGetPeriods()
    {
        $event = new Event(microtime(true));
        self::assertEquals([], $event->getPeriods());

        $event = new Event(microtime(true));
        $event->start();
        $event->stop();
        self::assertCount(1, $event->getPeriods());

        $event = new Event(microtime(true));
        $event->start();
        $event->stop();
        $event->start();
        $event->stop();
        self::assertCount(2, $event->getPeriods());
    }

    public function testLap()
    {
        $event = new Event(microtime(true));
        $event->start();
        $event->lap();
        $event->stop();
        self::assertCount(2, $event->getPeriods());
    }

    public function testDuration()
    {
        $event = new Event(microtime(true));
        $event->start();
        usleep(200000);
        $event->stop();
        self::assertLessThan(0.205, $event->getDuration(), null, self::DELTA);

        $event = new Event(microtime(true));
        $event->start();
        usleep(100000);
        $event->stop();
        usleep(50000);
        $event->start();
        usleep(100000);
        $event->stop();
        self::assertEquals(0.205, $event->getDuration(), null, self::DELTA);
    }

    public function testDurationBeforeStop()
    {
        $event = new Event(microtime(true));
        $event->start();
        usleep(200000);
        self::assertEquals(0.205, $event->getDuration(), null, self::DELTA);

        $event = new Event(microtime(true));
        $event->start();
        usleep(100000);
        $event->stop();
        usleep(50000);
        $event->start();
        usleep(100000);
        self::assertEquals(0.100, $event->getDuration(), null, self::DELTA);
    }

    /**
     * @expectedException \LogicException
     */
    public function testStopWithoutStart()
    {
        $event = new Event(microtime(true));
        $event->stop();
    }

    public function testIsStarted()
    {
        $event = new Event(microtime(true));
        $event->start();
        self::assertTrue($event->isStarted());
    }

    public function testIsNotStarted()
    {
        $event = new Event(microtime(true));
        self::assertFalse($event->isStarted());
    }

    public function testEnsureStopped()
    {
        // this also test overlap between two periods
        $event = new Event(microtime(true));
        $event->start();
        usleep(100000);
        $event->start();
        usleep(100000);
        $event->ensureStopped();
        self::assertEquals(0.301, $event->getDuration(), null, self::DELTA);
    }

    public function testStartTime()
    {
        $event = new Event(microtime(true));
        self::assertLessThanOrEqual(0.5, $event->getStartTime());

        $event = new Event(microtime(true));
        $event->start();
        $event->stop();
        self::assertLessThanOrEqual(1, $event->getStartTime());

        $event = new Event(microtime(true));
        $event->start();
        usleep(100000);
        $event->stop();
        self::assertEquals(0, $event->getStartTime(), null, self::DELTA);
    }

    public function testHumanRepresentation()
    {
        $event = new Event(microtime(true));
        self::assertEquals('default: 0.00 MiB - 0 ms', (string) $event);
        $event->start();
        $event->stop();
        self::assertEquals(1, preg_match('/default: [0-9\.]+ MiB - [0-9]+ ms/', (string) $event));

        $event = new Event(microtime(true), 'foo');
        self::assertEquals('foo: 0.00 MiB - 0 ms', (string) $event);
    }
}
