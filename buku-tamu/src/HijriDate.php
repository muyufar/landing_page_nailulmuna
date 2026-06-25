<?php

class HijriDate
{
    /**
     * Konversi tanggal Masehi ke Hijriah (algoritma Umm al-Qura sederhana).
     */
    public static function toHijri(?string $date = null): string
    {
        $timestamp = $date ? strtotime($date) : time();
        $jd = self::gregorianToJulian(
            (int) date('Y', $timestamp),
            (int) date('n', $timestamp),
            (int) date('j', $timestamp)
        );
        [$y, $m, $d] = self::julianToHijri($jd);

        $months = [
            1 => 'Muharram', 2 => 'Safar', 3 => 'Rabiul Awal', 4 => 'Rabiul Akhir',
            5 => 'Jumadil Awal', 6 => 'Jumadil Akhir', 7 => 'Rajab', 8 => 'Syaban',
            9 => 'Ramadhan', 10 => 'Syawal', 11 => 'Dzulqadah', 12 => 'Dzulhijjah',
        ];

        return sprintf('%d %s %d H', $d, $months[$m], $y);
    }

    private static function gregorianToJulian(int $y, int $m, int $d): float
    {
        if ($m <= 2) {
            $y--;
            $m += 12;
        }
        $a = floor($y / 100);
        $b = 2 - $a + floor($a / 4);
        return floor(365.25 * ($y + 4716)) + floor(30.6001 * ($m + 1)) + $d + $b - 1524.5;
    }

    private static function julianToHijri(float $jd): array
    {
        $jd = floor($jd) + 0.5;
        $l = $jd - 1948440 + 10632;
        $n = floor(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = floor((10985 - $l) / 5316) * floor((50 * $l) / 17719)
            + floor($l / 5670) * floor((43 * $l) / 15238);
        $l = $l - floor((30 - $j) / 15) * floor((17719 * $j) / 50)
            - floor($j / 16) * floor((15238 * $j) / 43) + 29;
        $m = floor((24 * $l) / 709);
        $d = $l - floor((709 * $m) / 24);
        $y = 30 * $n + $j - 30;
        return [(int) $y, (int) $m, (int) $d];
    }
}
