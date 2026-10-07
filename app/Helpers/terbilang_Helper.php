<?php

if (!function_exists('terbilang')) {
    function terbilang($angka): string
    {
        $angka = (int) $angka;
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($angka < 12) {
            return $huruf[$angka];
        }
        if ($angka < 20) {
            return terbilang($angka - 10) . ' belas';
        }
        if ($angka < 100) {
            return terbilang(intdiv($angka, 10)) . ' puluh ' . terbilang($angka % 10);
        }
        if ($angka < 200) {
            return 'seratus ' . terbilang($angka - 100);
        }
        if ($angka < 1000) {
            return terbilang(intdiv($angka, 100)) . ' ratus ' . terbilang($angka % 100);
        }
        if ($angka < 2000) {
            return 'seribu ' . terbilang($angka - 1000);
        }
        if ($angka < 1000000) {
            return terbilang(intdiv($angka, 1000)) . ' ribu ' . terbilang($angka % 1000);
        }
        if ($angka < 1000000000) {
            return terbilang(intdiv($angka, 1000000)) . ' juta ' . terbilang($angka % 1000000);
        }
        if ($angka < 1000000000000) {
            return terbilang(intdiv($angka, 1000000000)) . ' miliar ' . terbilang($angka % 1000000000);
        }
        return terbilang(intdiv($angka, 1000000000000)) . ' triliun ' . terbilang($angka % 1000000000000);
    }
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal): string
    {
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $ts = strtotime($tanggal);
        return date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    }
}