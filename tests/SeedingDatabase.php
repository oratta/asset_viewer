<?php
namespace Tests;

use Illuminate\Support\Facades\Artisan;
trait SeedingDatabase {
    protected function seedingDatabase()
    {
        Artisan::call('migrate:refresh --seed');
    }
}