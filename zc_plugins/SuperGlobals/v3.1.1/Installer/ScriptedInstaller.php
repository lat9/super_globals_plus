<?php
use Zencart\PluginSupport\ScriptedInstaller as ScriptedInstallBase;

class ScriptedInstaller extends ScriptedInstallBase
{
    protected string $configPageKey = 'configSuperglobals';

    protected string $configGroupTitle = 'Superglobals';

    protected int $cgi;

    /**
     * @return bool
     */
    protected function executeInstall()
    {
        if (!$this->purgeOldFiles()) {
            return false;
        }

        $this->cgi = $this->getOrCreateConfigGroupId($this->configGroupTitle, $this->configGroupTitle, null);

        // -----
        // If the plugin's configuration settings aren't present, add them now.
        //
        $this->executeInstallerSql(
            "INSERT IGNORE INTO " . TABLE_CONFIGURATION . "
                (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, date_added, sort_order, use_function, set_function)
             VALUES
                ('Enable Superglobals (catalog)', 'SHOW_SUPERGLOBALS', 'false', 'If true, the Superglobals will be shown in the shop at the bottom of the pages (and depending on the settings below).', $this->cgi, now(), 10, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Enable Superglobals (admin)', 'SHOW_SUPERGLOBALS_ADMIN', 'false', 'If true, the Superglobals will be shown in the admin at the top of pages (and depending on the settings below).', $this->cgi, now(), 15, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show Superglobals in Popup', 'SHOW_SUPERGLOBALS_POPUP', 'true', 'If true the Superglobals will be displayed in a popup window, using javascript. Set to false if you do not want or can not use javascript and the Superglobals will be displayed at the bottom of each page.', $this->cgi, now(), 18, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show the Superglobals to all (no IP check)', 'SHOW_SUPERGLOBALS_TO_ALL', 'false', 'If true, the Superglobals will be shown to every visitor (= Security risk).', $this->cgi, now(), 20, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('List of allowed IP addresses (comma separated list)', 'SHOW_SUPERGLOBALS_IP', '127.0.0.1,1,::1', 'Enter a comma separated list of allowed IP addresses. Default setting: 127.0.0.1,1,::1 (=localhost)', $this->cgi, now(), 30, NULL, NULL),

                ('Maximum recursion level', 'SHOW_SUPERGLOBALS_MAX_LEVEL', '12', 'Enter the maximum recursion level. Default setting: 12. This prevents infinite loops in case of recursion (" . '$GLOBALS' . " recursion is automatically detected). 0 disables the maximum recursion level protection', $this->cgi, now(), 32, NULL, NULL),

                ('Show " . '$GLOBALS' . " (shows all globals)', 'SHOW_SUPERGLOBALS_ALL', 'false', 'Show the contents of all Globals as well as all Super Globals. If enabled, the below settings for showing Super Globals will be overridden.', $this->cgi, now(), 34, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show the \"queryCache\" object?', 'SHOW_SUPERGLOBALS_QUERYCACHE', 'false', 'If <b>Show " . '$GLOBALS' . "</b> is set to \"true\", show the contents of the queryCache object?. If enabled, the amount of time required to format the " . '$GLOBALS' . " will be impacted.', $this->cgi, now(), 35, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Filter HTTP_ variables', 'SHOW_SUPERGLOBALS_FILTER_HTTP', 'true', 'If true, the <em>deprecated</em> HTTP_ variables are filtered out (recommended).', $this->cgi, now(), 36, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Super Globals Exclusions', 'SHOW_SUPERGLOBALS_EXCLUSIONS', 'configuration,saniGroup1,main_category_tree', 'Use this field to identify (using a packed, comma-separated list) any Super Globals that should <b>not</b> be displayed if <b>Show " . '$GLOBALS' . "</b> is set to \"true\".  These variables typically are large arrays that do not contain pertinent information.<br />Default: <em>configuration,saniGroup1,main_category_tree</em>', $this->cgi, now(), 37, NULL, NULL),

                ('Show " . '$_GET' . "', 'SHOW_SUPERGLOBALS_GET', 'false', 'Show the contents of this variable.', $this->cgi, now(), 40, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_POST' . "', 'SHOW_SUPERGLOBALS_POST', 'true', 'Show the contents of this variable.', $this->cgi, now(), 50, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_COOKIE' . "', 'SHOW_SUPERGLOBALS_COOKIE', 'false', 'Show the contents of this variable.', $this->cgi, now(), 60, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_REQUEST' . "', 'SHOW_SUPERGLOBALS_REQUEST', 'false', 'Show the contents of this variable.', $this->cgi, now(), 65, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_SESSION' . "', 'SHOW_SUPERGLOBALS_SESSION', 'true', 'Show the contents of this variable.', $this->cgi, now(), 70, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_SERVER' . "', 'SHOW_SUPERGLOBALS_SERVER', 'false', 'Show the contents of this variable.', $this->cgi, now(), 80, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_ENV' . "', 'SHOW_SUPERGLOBALS_ENV', 'false', 'Show the contents of this variable.', $this->cgi, now(), 90, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show " . '$_FILES' . "', 'SHOW_SUPERGLOBALS_FILES', 'false', 'Show the contents of this variable.', $this->cgi, now(), 100, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show defined CONSTANTS', 'SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS', 'false', 'Show all defined constants (Only switch on when needed, slows down page views!).', $this->cgi, now(), 110, NULL, 'zen_cfg_select_option([\'true\', \'false\'],'),

                ('Show included files', 'SHOW_SUPERGLOBALS_GET_INCLUDED_FILES', 'false', 'Show all included (and required) files.', $this->cgi, now(), 120, NULL, 'zen_cfg_select_option([\'true\', \'false\'],')"
        );

        if (!zen_page_key_exists($this->configPageKey)) {
            // -----
            // Register the plugin's configuration page for the admin menus.
            //
            zen_register_admin_page($this->configPageKey, 'BOX_CONFIGURATION_SUPERGLOBALS', 'FILENAME_CONFIGURATION', "gID=$this->cgi", 'configuration', 'Y');
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function executeUninstall()
    {
        zen_deregister_admin_pages($this->configPageKey);
        $this->deleteConfigurationGroup($this->configGroupTitle, true);
        return true;
    }

    protected function purgeOldFiles(): bool
    {
        $files_to_remove = [
            DIR_FS_ADMIN . 'includes/auto_loaders/config.SuperGlobalsAdmin.php',
            DIR_FS_ADMIN . 'includes/functions/extra_functions/superglobals.php',
            DIR_FS_ADMIN . 'includes/languages/english/extra_definitions/superglobals.php',
            DIR_FS_CATALOG . 'includes/auto_loaders/config.SuperGlobals.php',
            DIR_FS_CATALOG . 'includes/classes/observers/SuperGlobalsObserver.php',
            DIR_FS_CATALOG . 'includes/functions/extra_functions/superglobals.php',
            DIR_FS_CATALOG . 'includes/templates/template_default/css/superglobals.css',
        ];

        $error = false;
        foreach ($files_to_remove as $key => $next_file) {
            if (file_exists($next_file)) {
                $result = unlink($next_file);
                if (!$result && file_exists($next_file)) {
                    $error = true;
                    $this->errorContainer->addError(
                        0,
                        sprintf(ERROR_UNABLE_TO_DELETE_FILE, $next_file),
                        false,
                        // this str_replace has to do DIR_FS_ADMIN before CATALOG because catalog is contained within admin, so results are wrong.
                        // also, '[admin_directory]' is used to obfuscate the admin dir name, in case the user copy/pastes output to a public forum for help.
                        sprintf(ERROR_UNABLE_TO_DELETE_FILE, str_replace([DIR_FS_ADMIN, DIR_FS_CATALOG], ['[admin_directory]/', ''], $next_file))
                    );
                }
            }
        }
        return !$error;
    }
}
