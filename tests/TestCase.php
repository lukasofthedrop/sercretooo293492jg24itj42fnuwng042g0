<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Executa o DatabaseSeeder automaticamente durante os testes.
     */
    protected bool $seed = true;
}