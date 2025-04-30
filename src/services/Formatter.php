<?php
declare(strict_types=1);

namespace juni\twighelper\services;

use Craft;
use craft\base\Component;

final class Formatter extends Component
{
    public const MINUTE_IN_SECONDS =  60;
    public const HOUR_IN_SECONDS   =  60 * self::MINUTE_IN_SECONDS;
    public const DAY_IN_SECONDS    =  24 * self::HOUR_IN_SECONDS;
    public const WEEK_IN_SECONDS   =   7 * self::DAY_IN_SECONDS;
    public const MONTH_IN_SECONDS  =  30 * self::DAY_IN_SECONDS;
    public const YEAR_IN_SECONDS   = 365 * self::DAY_IN_SECONDS;

    /**
     * Determines the difference between two timestamps.
     *
     * The difference is returned in a human-readable format such as "1 hour",
     * "5 mins", "2 days".
     *
     * @since 1.5.0
     * @since 5.3.0 Added support for showing a difference in seconds.
     *
     * @param int $from Unix timestamp from which the difference begins.
     * @param int $to   Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
     * @return string Human-readable time difference.
     */
    public function toHumanTimeAgo(int $from, int $to = 0): string
    {
        if (empty($to)) {
            $to = time();
        }

        $diff = (int) abs($to - $from);

        if ($diff < self::MINUTE_IN_SECONDS) {
            $secs = $diff;
            if ($secs <= 1) {
                $secs = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{second} other{seconds}}', ['num' => $secs]);
        }
        elseif ($diff < self::HOUR_IN_SECONDS) {
            $mins = round($diff / self::MINUTE_IN_SECONDS);
            if ($mins <= 1) {
                $mins = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{minute} other{minutes}}', ['num' => $mins]);
        }
        elseif ( $diff < self::DAY_IN_SECONDS) {
            $hours = round($diff / self::HOUR_IN_SECONDS);
            if ( $hours <= 1 ) {
                $hours = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{hour} other{hours}}', ['num' => $hours]);
        }
        elseif ($diff < self::WEEK_IN_SECONDS) {
            $days = round($diff / self::DAY_IN_SECONDS);
            if ($days <= 1) {
                $days = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{day} other{days}}', ['num' => $days]);
        }
        elseif ($diff < self::MONTH_IN_SECONDS) {
            $weeks = round($diff / self::WEEK_IN_SECONDS);
            if ( $weeks <= 1 ) {
                $weeks = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{week} other{weeks}}', ['num' => $weeks]);
        }
        elseif ( $diff < self::YEAR_IN_SECONDS) {
            $months = round($diff / self::MONTH_IN_SECONDS);
            if ($months <= 1) {
                $months = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{month} other{months}}', ['num' => $months]);
        }
        else {
            $years = round($diff / self::YEAR_IN_SECONDS);
            if ($years <= 1) {
                $years = 1;
            }
            $since = Craft::t('app', '{num, number} {num, plural, =1{year} other{years}}', ['num' => $years]);
        }

        return $since;
    }
}
