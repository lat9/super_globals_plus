<?php
// -----
// Part of the Super Globals Plus plugin, provided by lat9.
// Copyright (c) 2019-2025, Vinos de Frutas Tropicales
//
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// -----
// Load and instantiate the SuperGlobalsAdmin class.  Loading at a very
// high checkpoint, in an attempt to gather **all** data provided by other
// late-loading plugins.
//
$autoLoadConfig[9999][] = [
    'autoType'  => 'class',
    'loadFile'  => 'SuperGlobalsAdmin.php',
];
$autoLoadConfig[9999][] = [
    'autoType'   => 'classInstantiate',
    'className'  => 'SuperGlobalsAdmin',
    'objectName' => 'SuperGlobalsAdmin'
];
