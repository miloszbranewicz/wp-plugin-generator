<?php

declare(strict_types=1);

namespace Pluginboilerplatevendor\Pluginboilerplate\Hook;

use Pluginboilerplatevendor\Pluginboilerplate\Service\ExampleService;

final class ExampleHook implements HookInterface
{
    public function __construct(private readonly ExampleService $exampleService)
    {
    }

    public function register(): void
    {
        add_action('wp_footer', [$this, 'doSomething']);
    }

    public function doSomething(): void
    {
        $this->exampleService->doSomething();
    }
}
