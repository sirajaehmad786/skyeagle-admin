<?php 

use Carbon\Carbon;

if(!function_exists('formateDate')){
    function formateDate($date, $formate='d-m-Y'){
        return Carbon::parse($date)->format($formate);
    }
}

if (! function_exists('formatAmount')) {
    function formatAmount($amount, $decimals = 2)
    {
        return number_format((float) $amount, $decimals, '.', '');
    }
}

if (!function_exists('countDaysAndNights')) {

    function countDaysAndNights($startDate, $endDate, $str=null)
    {
        $start = $startDate instanceof Carbon ? $startDate : Carbon::parse($startDate);
        $end   = $endDate instanceof Carbon ? $endDate : Carbon::parse($endDate);

        $days = $start->diffInDays($end) + 1;
        $nights = $days > 0 ? $days - 1 : 0;
        if($str == 1){
            return $nights . ' Nights, ' . $days. ' Days';
        }
        return [
            'days'   => $days,
            'nights' => $nights
        ];
    }
}

if (!function_exists('generateBookingId')) {
    function generateBookingId()
    {
        $prefix = config('constant.booking', 'SKB'); // BK
        $randomNumber = random_int(1000, 9999);     // 4 digit random
        return $prefix . $randomNumber;
    }
}


if (!function_exists('generatePaymentId')) {
    function generatePaymentId()
    {
        $prefix = config('constant.payment', 'PYB');
        $randomNumber = random_int(1000, 9999);
        return $prefix . $randomNumber;
    }
}

if (!function_exists('convertDateFormat')) {
    /**
     * Convert date from d-m-Y format to Y-m-d format
     * If date is already in Y-m-d format, return as is
     * 
     * @param string $date
     * @return string
     */
    function convertDateFormat($date)
    {
        if (empty($date)) {
            return $date;
        }
        
        try {
            // Try to parse as d-m-Y format
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            // If parsing fails, try Y-m-d format
            try {
                Carbon::createFromFormat('Y-m-d', $date);
                return $date; // Already in correct format
            } catch (\Exception $e2) {
                // If both fail, return original date (let database handle it)
                return $date;
            }
        }
    }
}
