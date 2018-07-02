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

namespace SerendipityHQ\Component\Stopwatch\Utils;

/**
 * Helper to format times and memories.
 *
 * Extracted from Symfony\Component\Console\Helper\Helper
 * and adapted to be used here in Stopwatch.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Adamo Crespi <hello@aerendir.me>
 */
class Formatter
{
    /**
     * If you need more precise measurements, increase the $precision to get more decimal digits.
     *
     * @param float $microtime
     * @param int   $precision
     *
     * @return string
     */
    public static function formatTime(float $microtime, int $precision = 2): string
    {
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
            if ($microtime >= $format[0]) {
                if ((isset($timeFormats[$index + 1]) && $microtime < $timeFormats[$index + 1][0])
                    || $index === count($timeFormats) - 1
                ) {
                    if (2 === count($format)) {
                        return round($microtime, $precision) . ' ' . $format[1];
                    }

                    return round($microtime / $format[2], $precision) . ' ' . $format[1];
                }
            }
        }

        throw new \RuntimeException('Impossible to format the time value you passed. This should never happen.');
    }

    /**
     * @param int $memory
     * @param int $precision
     *
     * @return string
     */
    public static function formatMemory(int $memory, int $precision = 2): string
    {
        if ($memory >= 1024 * 1024 * 1024) {
            $print = (float) $memory / 1024 / 1024 / 1024;

            return sprintf('%s GiB', round($print, $precision));
        }

        if ($memory >= 1024 * 1024) {
            $print = (float) $memory / 1024 / 1024;

            return sprintf('%s MiB', round($print, $precision));
        }

        if ($memory >= 1024) {
            $print = (float) $memory / 1024;

            return sprintf('%d KiB', round($print, $precision));
        }

        return sprintf('%d B', $memory);
    }
}
