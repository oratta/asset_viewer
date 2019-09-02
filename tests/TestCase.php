<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    static protected $databaseSetup = false;
    use CreatesApplication;

    public function setUp() : void
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        // 最初のみ実行するフラグを追加
        if (!static::$databaseSetup) {
            Artisan::call('migrate:refresh --seed');
            static::$databaseSetup = true;
        }
    }
}
