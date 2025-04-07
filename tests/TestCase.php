<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    // Run migrations before each test
    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate', ['--env' => 'testing']);
    }
}
