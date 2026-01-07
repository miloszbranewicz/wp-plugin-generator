<?php

declare(strict_types=1);

namespace Pluginboilerplatevendor\Pluginboilerplate\Service;

use Pluginboilerplatevendor\Pluginboilerplate\Plugin;

final class ViewRenderer
{
    public function render(string $templateName, array $args = []): void
    {
        $path = Plugin::getPath("templates/{$templateName}.php");

        if (!\file_exists($path)) {
            throw new \RuntimeException("Template {$templateName} not found");
        }

        \extract($args);
        include $path;
    }
}
