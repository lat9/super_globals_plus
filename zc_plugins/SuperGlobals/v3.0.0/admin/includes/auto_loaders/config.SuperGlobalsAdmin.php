<?php
// -----
// Part of the Super Globals Plus plugin, provided by lat9.
// Copyright (c) 2019-2025, Vinos de Frutas Tropicales
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// -----
// Load and instantiate the SuperGlobalsAdmin observer.  Loading at a very
// high checkpoint, in an attempt to gather **all** data provided by other
// late-loading plugins.
//
// Note: The same observer-class is used for both the admin and the storefront.
// 
$autoLoadConfig[9999][] = [
    'autoType'  => 'class',
    'loadFile'  => 'observers/SuperGlobalsObserver.php',
];
$autoLoadConfig[9999][] = [
    'autoType'   => 'classInstantiate',
    'className'  => 'SuperGlobalsObserver',
    'objectName' => 'SuperGlobalsAdmin'
];
