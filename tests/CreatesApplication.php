<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Import the application bootstrap file
        $app = require_once __DIR__.'/../bootstrap/app.php';

        // Bootstrap the application
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
