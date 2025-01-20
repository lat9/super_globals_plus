<?php
// -----
// Starting with v1.6.0 of the plugin, perform the auto-install of the various configuration items.
//
$configurationGroupTitle = 'Superglobals';
$configuration = $db->Execute("SELECT configuration_group_id FROM " . TABLE_CONFIGURATION_GROUP . " WHERE configuration_group_title = '$configurationGroupTitle' LIMIT 1");
if ($configuration->EOF) {
  $db->Execute("INSERT INTO " . TABLE_CONFIGURATION_GROUP . " 
                 (configuration_group_title, configuration_group_description, sort_order, visible) 
                 VALUES ('$configurationGroupTitle', 'Superglobals Settings', '1', '1');");
  $configuration_group_id = $db->Insert_ID(); 
  $db->Execute("UPDATE " . TABLE_CONFIGURATION_GROUP . " SET sort_order = $configuration_group_id WHERE configuration_group_id = $configuration_group_id;");
  
} else {
  $configuration_group_id = $configuration->fields['configuration_group_id'];
  
}

// -----
// Set the various configuration items, if Super Globals wasn't previously installed.
//
if (!defined ('SHOW_SUPERGLOBALS')) {
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Enable Superglobals (catalog)', 'SHOW_SUPERGLOBALS', 'false', 'If true, the Superglobals will be shown in the shop at the bottom of the pages (and depending on the settings below).', $configuration_group_id, 10, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Enable Superglobals (admin)', 'SHOW_SUPERGLOBALS_ADMIN', 'false', 'If true, the Superglobals will be shown in the admin at the top of pages (and depending on the settings below).', $configuration_group_id, 15, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show Superglobals in Popup', 'SHOW_SUPERGLOBALS_POPUP', 'true', 'If true the Superglobals will be displayed in a popup window, using javascript. Set to false if you do not want or can not use javascript and the Superglobals will be displayed at the bottom of each page.', $configuration_group_id, 18, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show the Superglobals to all (no IP check)', 'SHOW_SUPERGLOBALS_TO_ALL', 'false', 'If true, the Superglobals will be shown to every visitor (= Security risk).', $configuration_group_id, 20, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'List of allowed IP addresses (comma separated list)', 'SHOW_SUPERGLOBALS_IP', '127.0.0.1,1,::1', 'Enter a comma separated list of allowed IP addresses. Default setting: 127.0.0.1,1,::1 (=localhost)', $configuration_group_id, 30, NULL, NULL)");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Maximum recursion level', 'SHOW_SUPERGLOBALS_MAX_LEVEL', '12', 'Enter the maximum recursion level. Default setting: 12. This prevents infinite loops in case of recursion (" . '$GLOBALS' . " recursion is automatically detected). 0 disables the maximum recursion level protection', $configuration_group_id, 32, NULL, NULL)");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$GLOBALS' . " (shows all globals)', 'SHOW_SUPERGLOBALS_ALL', 'false', 'Show the contents of all Globals as well as all Super Globals. If enabled, the below settings for showing Super Globals will be overridden.', $configuration_group_id, 34, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show the \"queryCache\" object?', 'SHOW_SUPERGLOBALS_QUERYCACHE', 'false', 'If <b>Show " . '$GLOBALS' . "</b> is set to \"true\", show the contents of the queryCache object?. If enabled, the amount of time required to format the " . '$GLOBALS' . " will be impacted.', $configuration_group_id, 35, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Filter HTTP_ variables', 'SHOW_SUPERGLOBALS_FILTER_HTTP', 'true', 'If true, the <em>deprecated</em> HTTP_ variables are filtered out (recommended).', $configuration_group_id, 36, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_GET' . "', 'SHOW_SUPERGLOBALS_GET', 'false', 'Show the contents of this variable.', $configuration_group_id, 40, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_POST' . "', 'SHOW_SUPERGLOBALS_POST', 'true', 'Show the contents of this variable.', $configuration_group_id, 50, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_COOKIE' . "', 'SHOW_SUPERGLOBALS_COOKIE', 'false', 'Show the contents of this variable.', $configuration_group_id, 60, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_REQUEST' . "', 'SHOW_SUPERGLOBALS_REQUEST', 'false', 'Show the contents of this variable.', $configuration_group_id, 65, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_SESSION' . "', 'SHOW_SUPERGLOBALS_SESSION', 'true', 'Show the contents of this variable.', $configuration_group_id, 70, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_SERVER' . "', 'SHOW_SUPERGLOBALS_SERVER', 'false', 'Show the contents of this variable.', $configuration_group_id, 80, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_ENV' . "', 'SHOW_SUPERGLOBALS_ENV', 'false', 'Show the contents of this variable.', $configuration_group_id, 90, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show " . '$_FILES' . "', 'SHOW_SUPERGLOBALS_FILES', 'false', 'Show the contents of this variable.', $configuration_group_id, 100, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show defined CONSTANTS', 'SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS', 'false', 'Show all defined constants (Only switch on when needed, slows down page views!).', $configuration_group_id, 110, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");

  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Show included files', 'SHOW_SUPERGLOBALS_GET_INCLUDED_FILES', 'false', 'Show all included (and required) files.', $configuration_group_id, 120, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),')");
  
}
if (!defined ('SHOW_SUPERGLOBALS_EXCLUSIONS')) {
  $db->Execute ("INSERT INTO " . TABLE_CONFIGURATION . " ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function ) VALUES ( 'Super Globals Exclusions', 'SHOW_SUPERGLOBALS_EXCLUSIONS', 'configuration,saniGroup1,main_category_tree', 'Use this field to identify (using a packed, comma-separated list) any Super Globals that should <b>not</b> be displayed if <b>Show " . '$GLOBALS' . "</b> is set to \"true\".  These variables typically are large arrays that do not contain pertinent information.<br />Default: <em>configuration,saniGroup1,main_category_tree</em>', $configuration_group_id, 37, NULL, NULL)");

}
//----
// If the installation supports admin-page registration (i.e. v1.5.0 and later), then register the Superglobals configuration.
//
if (function_exists('zen_register_admin_page')) {
  if (!zen_page_key_exists('configSuperglobals')) {
    zen_register_admin_page('configSuperglobals', 'BOX_CONFIGURATION_SUPERGLOBALS', 'FILENAME_CONFIGURATION', "gID=$configuration_group_id", 'configuration', 'Y', $configuration_group_id);
  }
}  
// -----
// Pull in the echo_superglobals() function.
//
define('SHOW_SUPERGLOBALS_FROM_ADMIN', TRUE);
include (DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/superglobals.php');