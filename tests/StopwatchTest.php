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
use SerendipityHQ\Component\Stopwatch\Section;
use SerendipityHQ\Component\Stopwatch\Stopwatch;

/**
 * StopwatchTest.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @group time-sensitive
 */
class StopwatchTest extends TestCase
{
    private const DELTA = 0.005;

    public function testStart()
    {
        $stopwatch = new Stopwatch();
        $event     = $stopwatch->start('foo', 'cat');

        self::assertInstanceOf(Event::class, $event);
        self::assertEquals('cat', $event->getCategory());
        self::assertSame($event, $stopwatch->getEvent('foo'));
    }

    public function testIsStarted()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('foo', 'cat');

        self::assertTrue($stopwatch->isStarted('foo'));
    }

    public function testIsNotStarted()
    {
        $stopwatch = new Stopwatch();

        self::assertFalse($stopwatch->isStarted('foo'));
    }

    public function testIsNotStartedEvent()
    {
        $stopwatch = new Stopwatch();

        $sections = new \ReflectionProperty(Stopwatch::class, 'sections');
        $sections->setAccessible(true);
        $section = $sections->getValue($stopwatch);

        $events = new \ReflectionProperty(Section::class, 'events');
        $events->setAccessible(true);

        $stopwatchMockEvent = $this->getMockBuilder(Event::class)
            ->setConstructorArgs([microtime(true) * 1000])
            ->getMock();

        $events->setValue(end($section), ['foo' => $stopwatchMockEvent]);

        self::assertFalse($stopwatch->isStarted('foo'));
    }

    public function testStop()
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('foo', 'cat');
        usleep(200000);
        $event = $stopwatch->stop('foo');

        self::assertEqualsWithDelta(0.204, $event->getDuration(), self::DELTA);
    }

    public function testUnknownEvent()
    {
        $this->expectException(\LogicException::class);
        $stopwatch = new Stopwatch();
        $stopwatch->getEvent('foo');
    }

    public function testStopWithoutStart()
    {
        $this->expectException(\LogicException::class);
        $stopwatch = new Stopwatch();
        $stopwatch->stop('foo');
    }

    public function testSection()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->openSection();
        $stopwatch->start('foo', 'cat');
        $stopwatch->stop('foo');
        $stopwatch->start('bar', 'cat');
        $stopwatch->stop('bar');
        $stopwatch->stopSection('1');

        $stopwatch->openSection();
        $stopwatch->start('foobar', 'cat');
        $stopwatch->stop('foobar');
        $stopwatch->stopSection('2');

        $stopwatch->openSection();
        $stopwatch->start('foobar', 'cat');
        $stopwatch->stop('foobar');
        $stopwatch->stopSection('0');

        // the section is an event by itself
        self::assertCount(3, $stopwatch->getSectionEvents('1'));
        self::assertCount(2, $stopwatch->getSectionEvents('2'));
        self::assertCount(2, $stopwatch->getSectionEvents('0'));
    }

    public function testReopenASection()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->openSection();
        $stopwatch->start('foo', 'cat');
        $stopwatch->stopSection('section');

        $stopwatch->openSection('section');
        $stopwatch->start('bar', 'cat');
        $stopwatch->stopSection('section');

        $events = $stopwatch->getSectionEvents('section');

        self::assertCount(3, $events);
        self::assertCount(2, $events['__section__']->getPeriods());
    }

    public function testReopenANewSectionShouldThrowAnException()
    {
        $this->expectException(\LogicException::class);
        $stopwatch = new Stopwatch();
        $stopwatch->openSection('section');
    }

    public function testReset()
    {
        $stopwatch = new Stopwatch();

        $stopwatch->openSection();
        $stopwatch->start('foo', 'cat');

        $stopwatch->reset();

        $sections = $stopwatch->getSections();

        // Root section is equal to private constant Stopwatch::STOPWATCH_ROOT
        $rootSection = '__root__';
        self::assertCount(1, $sections);
        self::assertArrayHasKey($rootSection, $sections);
        self::assertEmpty($sections[$rootSection]->getEvents());
    }
}
