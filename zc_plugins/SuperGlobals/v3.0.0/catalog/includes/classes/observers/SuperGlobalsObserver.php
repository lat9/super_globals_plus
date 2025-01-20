<?php
// -----
// Part of the Super Globals Plus plugin, provided by lat9.
// Copyright (c) 2019-2025, Vinos de Frutas Tropicales
//
use Zencart\Traits\InteractsWithPlugins;

class SuperGlobalsObserver extends \base
{
    use InteractsWithPlugins;

    // -----
    // Class constructor.
    //
    public function __construct()
    {
        // -----
        // Watch for the end-of-page indicators from both the admin and storefront.
        //
        // Note: Earlier versions of Zen Cart might not provide these notifications, but the
        // output will be generated ... so long as the associated files are modified, per the
        // plugin's readme.
        //
        $this->attach(
            $this,
            [
                //- From /admin/footer.php
                'NOTIFY_ADMIN_FOOTER_END',

                //- From /admin/index.php
                'NOTIFY_ADMIN_INDEX_END',

                //- From /includes/templates/YOUR_TEMPLATE/common/tpl_main_page.php
                'NOTIFY_FOOTER_END',
            ]
        );
    }

    // -----
    // This function is invoked when the attached notifiers "fires" and results in the superglobals'
    // output being rendered.
    //
    public function update(&$class, string $eventID)
    {
        switch ($eventID) {
            // -----
            // Admin end-of-page.
            //
            case 'NOTIFY_ADMIN_FOOTER_END':
            case 'NOTIFY_ADMIN_INDEX_END':
                if (defined('SHOW_SUPERGLOBALS_ADMIN') && SHOW_SUPERGLOBALS_ADMIN === 'true') {
                    $css_location = $this->loadFiles();
                    echo superglobals_echo($css_location);
                }
                break;

            // -----
            // Storefront end-of-page.
            //
            case 'NOTIFY_FOOTER_END':
                if (defined('SHOW_SUPERGLOBALS') && SHOW_SUPERGLOBALS === 'true') {
                    $css_location = $this->loadFiles();
                    echo superglobals_echo($css_location);
                }
                break;

            default:
                break;
        }
    }

    protected function loadFiles(): string
    {
        // -----
        // Use the base trait to determine this plugin's directory location.
        //
        $this->detectZcPluginDetails(__DIR__);
        $catalog_dir = $this->pluginManagerInstalledVersionDirectory . 'catalog/';

        // -----
        // Load the processing function file.
        //
        require $catalog_dir . 'functions/superglobals.php';

        // -----
        // Return the location of the plugin's CSS file.
        //
        return $catalog_dir . 'templates/default/css/superglobals.css';
    }
}
