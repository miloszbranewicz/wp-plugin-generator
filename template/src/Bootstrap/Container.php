<?php

declare(strict_types=1);

namespace Pluginboilerplatevendor\Pluginboilerplate\Bootstrap;

use Pluginboilerplatevendor\Pluginboilerplate\Hook\AssetsHook;
use Pluginboilerplatevendor\Pluginboilerplate\Hook\ExampleHook;
use Pluginboilerplatevendor\Pluginboilerplate\Service\ExampleService;
use Pluginboilerplatevendor\Pluginboilerplate\Service\ViewRenderer;

final class Container
{
    private array $instances = [];

    public function getRegisteredHooks(): array
    {
        return [
            new ExampleHook($this->exampleService()),
            new AssetsHook(),
        ];
    }

    public function exampleService(): ExampleService
    {
        return $this->instances[ExampleService::class] ??= new ExampleService(
            $this->viewRenderer(),
        );
    }

    public function viewRenderer(): ViewRenderer
    {
        return $this->instances[ViewRenderer::class] ??= new ViewRenderer();
    }
}
