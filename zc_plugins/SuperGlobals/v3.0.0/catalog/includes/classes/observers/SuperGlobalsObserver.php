<?php
// -----
// Part of the Super Globals Plus plugin, provided by lat9.
// Copyright (c) 2019-2025, Vinos de Frutas Tropicales
//
class SuperGlobalsObserver extends base 
{
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
    public function update(&$class, $eventID, $p1, &$p2, &$p3, &$p4, &$p5)
    {
        switch ($eventID) {
            // -----
            // Admin end-of-page.
            //
            case 'NOTIFY_ADMIN_FOOTER_END':
            case 'NOTIFY_ADMIN_INDEX_END':
                if (defined('SHOW_SUPERGLOBALS_ADMIN') && SHOW_SUPERGLOBALS_ADMIN === 'true') {
                    echo superglobals_echo();
                }
                break;

            // -----
            // Storefront end-of-page.
            //
            case 'NOTIFY_FOOTER_END':
                if (defined('SHOW_SUPERGLOBALS') && SHOW_SUPERGLOBALS === 'true') {
                    echo superglobals_echo();
                }
                break;

            default:
                break;
        }
    }
}
