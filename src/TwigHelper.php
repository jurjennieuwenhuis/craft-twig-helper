<?php
declare(strict_types=1);

namespace juni\twighelper;

use Craft;
use craft\base\Plugin;
use juni\twighelper\services\ServicesTrait;
use juni\twighelper\twigextensions\TwigExtension;

final class TwigHelper extends Plugin
{
    // Traits
    // =========================================================================

    use ServicesTrait;

    // Static properties
    // =========================================================================

    public static ?TwigHelper $plugin;

    // Public methods
    // =========================================================================

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function() {
            $this->_registerTwigExtension();
        });
    }

    private function _registerTwigExtension(): void
    {
        // Only use on the frontend
        if (!Craft::$app->getRequest()->getIsSiteRequest()) {
            return;
        }

        Craft::$app->view->registerTwigExtension(new TwigExtension());
    }
}
