<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bersihkan audit lama setiap hari; arsip JSONL ditulis lebih dulu.
Schedule::command('audit:prune')->dailyAt('02:30');
