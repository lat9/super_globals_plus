<?php
// -----
// Part of the Super Globals Plus plugin, provided by lat9.
// Copyright (c) 2025, Vinos de Frutas Tropicales
//
use Zencart\Traits\InteractsWithPlugins;

class SuperGlobalsAdmin
{
    use InteractsWithPlugins;

    // -----
    // Class constructor.
    //
    public function __construct()
    {
        $this->detectZcPluginDetails(__DIR__);
        $catalog_dir = $this->pluginManagerInstalledVersionDirectory . 'catalog/';

        require $catalog_dir . 'includes/classes/observers/SuperGlobalsObserver.php';
        $sga_observer = new SuperGlobalsObserver();
    }
}
