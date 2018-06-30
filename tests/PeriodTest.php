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
use SerendipityHQ\Component\Stopwatch\Period;

/**
 * Tests the Period.
 */
class PeriodTest extends TestCase
{
    /**
     * @dataProvider provideTimeValues
     *
     * @param mixed $start
     * @param mixed $expected
     */
    public function testGetStartTime($start, $expected)
    {
        $period = new Period($start, $start);
        self::assertSame($expected, $period->getStartTime());
    }

    /**
     * @dataProvider provideTimeValues
     *
     * @param mixed $end
     * @param mixed $expected
     */
    public function testGetEndTime($end, $expected)
    {
        $period = new Period($end, $end);
        self::assertSame($expected, $period->getEndTime());
    }

    /**
     * @dataProvider provideDurationValues
     *
     * @param mixed $start
     * @param mixed $end
     * @param mixed $duration
     */
    public function testGetDuration($start, $end, $duration)
    {
        $period = new Period($start, $end);
        self::assertSame($duration, $period->getDuration());
    }

    /**
     * @return \Generator
     */
    public function provideTimeValues(): \Generator
    {
        yield [0.0, 0.0];
        yield [2.71, 2.71];
    }

    /**
     * @return \Generator
     */
    public function provideDurationValues(): \Generator
    {
        yield [0, 0, 0.0];
        yield [0.0, 0.0, 0.0];
        yield [2, 3.14, 1.14];
        yield [2.71, 3.14, 0.43];
    }
}
