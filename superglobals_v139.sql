# Superglobals Plus
# 2007/10/15
# Use phpMyAdmin (add table prefixes if needed)
# Or use the Zen Cart sql tool (it has bugs but copy and paste *usually* works, the upload feature is NOT recommended!).
# remove Superglobals settings
# 2006/03/03

# BOF remove old Super Globals Plus configurations
SELECT @superglobalsid:=configuration_group_id
FROM configuration_group WHERE configuration_group_title="Superglobals";

DELETE FROM configuration WHERE configuration_group_id=@superglobalsid and configuration_group_id != 0;

DELETE FROM configuration_group WHERE configuration_group_id=@superglobalsid and configuration_group_id != 0;
# EOF remove old Super Globals Plus configurations

SELECT @sortorder:=max(sort_order)
FROM configuration_group;

INSERT INTO configuration_group
VALUES (NULL , 'Superglobals', 'Settings for the Superglobals script.', @sortorder+1, 1);

SELECT @superglobalsid:=max(configuration_group_id)
FROM configuration_group;

#SELECT @superglobalsid;

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Enable Superglobals (catalog)', 'SHOW_SUPERGLOBALS', 'false', 'If true, the Superglobals will be shown in the shop at the bottom of the pages (and depending on the settings below).', @superglobalsid , 10, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Enable Superglobals (admin)', 'SHOW_SUPERGLOBALS_ADMIN', 'false', 'If true, the Superglobals will be shown in the admin at the top of pages (and depending on the settings below).', @superglobalsid , 15, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show Superglobals in Popup', 'SHOW_SUPERGLOBALS_POPUP', 'true', 'If true the Superglobals will be displayed in a popup window, using javascript. Set to false if you do not want or can not use javascript and the Superglobals will be displayed at the bottom of each page.', @superglobalsid , 18, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),');

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show the Superglobals to all (no ip check)', 'SHOW_SUPERGLOBALS_TO_ALL', 'false', 'If true, the Superglobals will be shown to every visitor (= Security risk).', @superglobalsid , 20, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'List of allowed ip addresses (comma separated list)', 'SHOW_SUPERGLOBALS_IP', '127.0.0.1', 'Enter a comma separated list of allowed ip adresses. Default setting: 127.0.0.1 (=localhost)', @superglobalsid , 30, NULL , NULL );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Maximum recursion level', 'SHOW_SUPERGLOBALS_MAX_LEVEL', '12', 'Enter the maximum recursion level. Default setting: 12. This prevents infinite loops in case of recursion ($GLOBALS recursion is automaticly detected). 0 disables the maximum recursion level protection', @superglobalsid , 32, NULL , NULL );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $GLOBALS (shows all globals)', 'SHOW_SUPERGLOBALS_ALL', 'false', 'Show the contents of all Globals as well as all Super Globals. If enabled, the below settings for showing Super Globals will be overridden.', @superglobalsid , 34, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Filter HTTP_ variables', 'SHOW_SUPERGLOBALS_FILTER_HTTP', 'true', 'If true, the "depricated" HTTP_ variables are filtered out (recommended).', @superglobalsid , 36, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_GET', 'SHOW_SUPERGLOBALS_GET', 'false', 'Show the contents of this variable.', @superglobalsid , 40, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_POST', 'SHOW_SUPERGLOBALS_POST', 'true', 'Show the contents of this variable.', @superglobalsid , 50, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_COOKIE', 'SHOW_SUPERGLOBALS_COOKIE', 'false', 'Show the contents of this variable.', @superglobalsid , 60, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_REQUEST', 'SHOW_SUPERGLOBALS_REQUEST', 'false', 'Show the contents of this variable.', @superglobalsid , 65, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_SESSION', 'SHOW_SUPERGLOBALS_SESSION', 'true', 'Show the contents of this variable.', @superglobalsid , 70, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_SERVER', 'SHOW_SUPERGLOBALS_SERVER', 'false', 'Show the contents of this variable.', @superglobalsid , 80, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_ENV', 'SHOW_SUPERGLOBALS_ENV', 'false', 'Show the contents of this variable.', @superglobalsid , 90, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $_FILES', 'SHOW_SUPERGLOBALS_FILES', 'false', 'Show the contents of this variable.', @superglobalsid , 100, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show defined CONSTANTS', 'SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS', 'false', 'Show all defined constants (Only switch on when needed, slows down page views!).', @superglobalsid , 110, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

#SHOW_SUPERGLOBALS_GET_INCLUDED_FILES
INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show included files', 'SHOW_SUPERGLOBALS_GET_INCLUDED_FILES', 'false', 'Show all included (and required) files.', @superglobalsid , 120, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );
