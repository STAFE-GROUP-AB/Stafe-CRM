<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Storage::extend('google', function ($app, $config) {
            // IMPORTANT: useDisplayPaths must be false to use folder IDs correctly
            $options = [
                'useDisplayPaths' => false,
            ];

            if (! empty($config['teamDriveId'] ?? null)) {
                $options['teamDriveId'] = $config['teamDriveId'];
            }

            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/', $options);
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter, $config);
        });
    }
}
