<?php

declare(strict_types=1);

namespace Pluginboilerplatevendor\Pluginboilerplate\Hook;

use Pluginboilerplatevendor\Pluginboilerplate\Plugin;

final class AssetsHook implements HookInterface
{
    public function register(): void
    {
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontStyles']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontScripts']);
    }

    public function enqueueFrontStyles(): void
    {
        wp_enqueue_style('pluginboilerplate-style', Plugin::getUrl('assets/css/style.css'), [], Plugin::getVersion());
    }

    public function enqueueFrontScripts(): void
    {
        $handle = 'pluginboilerplate-script';
        wp_enqueue_script($handle, Plugin::getUrl('assets/js/script.js'), ['jquery'], Plugin::getVersion(), true);

        wp_localize_script($handle, 'pluginboilerplate_vars', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
}
