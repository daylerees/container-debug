[![Build Status](https://travis-ci.org/daylerees/container-debug.png)](https://travis-ci.org/daylerees/container-debug.png)

# Container Debug

The container debug command can be used to inspect the Laravel four service container. It's similar to the container:debug from the Symfony framework. It will show a list of all bindings, their types, values if scalar, and the time it takes to resolve each binding.

## Installation

Add the following dependency to your Laravel project:

    "daylerees/container-debug": "4.0.*"

Now run...

    composer update

to download the package. Now add the service provider to your project configuration at `app/config/app.php`.

    'providers' => array(
        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        'Illuminate\Cache\CacheServiceProvider',
        'Illuminate\Foundation\Providers\CommandCreatorServiceProvider',
        --- more ---
        'DayleRees\ContainerDebug\ServiceProvider'
    ),

Now you can execute the command using the Artisan CLI tool.

## Usage

    php artisan container:debug

You will see an output similar to the following.

    +--------------------------+------------------------------------------------------------+----------------------+
    | Identifier               | Service                                                    | Resolution time (ms) |
    +--------------------------+------------------------------------------------------------+----------------------+
    | artisan                  | Illuminate\Console\Application                             | 0.011                |
    | asset.publisher          | Illuminate\Foundation\AssetPublisher                       | 0.020                |
    | auth                     | Illuminate\Auth\AuthManager                                | 0.023                |
    | auth.reminder            | Unable to resolve service.                                 | N/A                  |
    | auth.reminder.repository | Unable to resolve service.                                 | N/A                  |
    | cache                    | Illuminate\Cache\CacheManager                              | 0.044                |
    | command.asset.publish    | Illuminate\Foundation\Console\AssetPublishCommand          | 0.058                |
    | command.auth.reminders   | Illuminate\Auth\Console\MakeRemindersCommand               | 0.029                |
    | command.cache.clear      | Illuminate\Cache\Console\ClearCommand                      | 0.023                |
    | command.dump-autoload    | Illuminate\Foundation\Console\AutoloadCommand              | 0.022                |
    | command.environment      | Illuminate\Foundation\Console\EnvironmentCommand           | 0.023                |
    | command.key.generate     | Illuminate\Foundation\Console\KeyGenerateCommand           | 0.022                |
    | env                      | <string> "production"                                      | 0.017                |
    | events                   | Illuminate\Events\Dispatcher                               | 0.018                |
    | exception                | Illuminate\Exception\Handler                               | 0.019                |
    | exception.debug          | Illuminate\Exception\WhoopsDisplayer                       | 0.018                |
    +--------------------------+------------------------------------------------------------+----------------------+

The first column contains the identifier for the service within the IoC container.

The second column contains information about the service which depends on its type:
- (Scalar Value) The type and value. e.g <string> "foo"
- (Non Scalar Value) The type of the value. e.g <array>
- (Object) The class name of the object. e.g DateTime

If an exception is thrown when the service is resolved, then the column will show 'Unable to resolve service.'.

The third column displays the time taken to resolve the service from the IoC container in milliseconds.

## Possible Uses

- Quick lookup of container objects : php artisan container:debug | grep "mail"
- Find which classes are represented by Facades to container objects.
- Diagnose performance issues by studying resolution times of container objects.

Enjoy, and as always, star the repo and tell your friends!
