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

namespace SerendipityHQ\Component\Stopwatch\Utils;

use RuntimeException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;

/**
 * Helper to format times and memories.
 *
 * Extracted from Symfony\Component\Console\Helper\Helper
 * and adapted to be used here in Stopwatch.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
final class Formatter
{
    /**
     * If you need more precise measurements, increase the $precision to get more decimal digits.
     *
     * @param float $microtime
     * @param int   $precision
     *
     * @throws StringsException
     * @throws RuntimeException If the passed time cannot be formatted
     *
     * @return string
     */
    public static function formatTime(float $microtime, int $precision = 2): string
    {
        $timeFormats        = null;
        static $timeFormats = [
            [0, 'ms'],
            [1, '1 sec'],
            [2, 'secs', 1],
            [60, '1 min'],
            [120, 'mins', 60],
            [3600, '1 hr'],
            [7200, 'hrs', 3600],
            [86400, '1 day'],
            [172800, 'days', 86400],
        ];

        foreach ($timeFormats as $index => $format) {
            if ($microtime >= $format[0] && ((isset($timeFormats[$index + 1]) && $microtime < $timeFormats[$index + 1][0]) || $index === (\is_countable($timeFormats) ? \count($timeFormats) : 0) - 1)) {
                if (2 === (\is_countable($format) ? \count($format) : 0)) {
                    return sprintf('%s %s', \round($microtime, $precision), $format[1]);
                }

                return sprintf('%s %s', \round($microtime / $format[2], $precision), $format[1]);
            }
        }

        throw new RuntimeException('Impossible to format the time value you passed. This should never happen.');
    }

    /**
     * @param int $memory
     * @param int $precision
     *
     * @throws StringsException
     *
     * @return string
     */
    public static function formatMemory(int $memory, int $precision = 2): string
    {
        if (\abs($memory) >= 1024 * 1024 * 1024) {
            /** @psalm-suppress InvalidOperand */
            $print = $memory / 1024 / 1024 / 1024;

            return sprintf('%s GiB', \round($print, $precision));
        }

        if (\abs($memory) >= 1024 * 1024) {
            /** @psalm-suppress InvalidOperand */
            $print = $memory / 1024 / 1024;

            return sprintf('%s MiB', \round($print, $precision));
        }

        if (\abs($memory) >= 1024) {
            $print = $memory / 1024;

            return sprintf('%d KiB', \round($print, $precision));
        }

        return sprintf('%d B', $memory);
    }
}
