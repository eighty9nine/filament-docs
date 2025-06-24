<?php

namespace EightyNine\FilamentDocs\Tests;

use Filament\FilamentServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use EightyNine\FilamentDocs\FilamentDocsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set up basic configuration
        $this->app['config']->set('database.default', 'testing');
        $this->app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Load package configuration
        $this->loadPackageConfig();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FilamentDocsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // Set up database for testing
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Set up other testing configurations
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('app.env', 'testing');
    }

    protected function loadPackageConfig()
    {
        // Load the package configuration
        $configPath = __DIR__ . '/../config/filament-docs.php';
        if (file_exists($configPath)) {
            $config = include $configPath;
            $this->app['config']->set('filament-docs', $config);
        }
    }

    protected function defineDatabaseMigrations()
    {
        // Only load migrations if needed for specific tests
    }
}
