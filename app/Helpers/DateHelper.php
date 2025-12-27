<?php

namespace App\Helpers;

use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class DateHelper
{
    /**
     * تبدیل تاریخ میلادی به شمسی برای نمایش
     * 
     * @param string|null $input
     * @return string
     */
    public static function toJalaliDisplay(?string $input): string
    {
        if (empty($input)) {
            return '';
        }

        try {
            return Jalalian::fromDateTime(Carbon::parse($input))->format('Y/m/d');
        } catch (\Throwable $e) {
            return '';
        }
    }
}
