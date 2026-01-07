<?php

declare(strict_types=1);

namespace Pluginboilerplatevendor\Pluginboilerplate\Service;

final class ExampleService
{
    public function __construct(private readonly ViewRenderer $viewRenderer)
    {
    }

    public function doSomething(): void
    {
        $this->viewRenderer->render('example-template', ['title' => 'Example title']);
    }
}
