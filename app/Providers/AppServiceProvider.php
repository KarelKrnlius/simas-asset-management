<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nama bulan Indonesia
        $bulanId = [
            1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
            4  => 'April',    5  => 'Mei',       6  => 'Juni',
            7  => 'Juli',     8  => 'Agustus',   9  => 'September',
            10 => 'Oktober',  11 => 'November',  12 => 'Desember',
        ];

        /**
         * Carbon macro: formatId()
         * Contoh hasil: "19 Mei 2026"
         */
        Carbon::macro('formatId', function () use ($bulanId) {
            /** @var Carbon $this */
            return $this->day . ' ' . $bulanId[$this->month] . ' ' . $this->year;
        });

        /**
         * Carbon macro: formatIdDateTime()
         * Contoh hasil: "19 Mei 2026, 14:30"
         */
        Carbon::macro('formatIdDateTime', function () use ($bulanId) {
            /** @var Carbon $this */
            return $this->day . ' ' . $bulanId[$this->month] . ' ' . $this->year
                . ', ' . $this->format('H:i');
        });
    }
}
