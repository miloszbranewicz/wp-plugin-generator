<?php

declare(strict_types=1);

namespace Pluginboilerplatevendor\Pluginboilerplate;

use Pluginboilerplatevendor\Pluginboilerplate\Bootstrap\Container;

final class Plugin
{
    private static Container $container;
    private static string $pluginFile;
    private static string $version = '1.0.0';

    public static function run(string $file): void
    {
        self::$pluginFile = $file;
        self::$container = new Container();
        foreach (self::$container->getRegisteredHooks() as $hook) {
            $hook->register();
        }
    }

    public static function getContainer(): Container
    {
        return self::$container;
    }

    public static function getPath(string $subPath = ''): string
    {
        return plugin_dir_path(self::$pluginFile) . \ltrim($subPath, '/');
    }

    public static function getUrl(string $subPath = ''): string
    {
        return plugin_dir_url(self::$pluginFile) . \ltrim($subPath, '/');
    }

    public static function getVersion(): string
    {
        return self::$version;
    }
}
