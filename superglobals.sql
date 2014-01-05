# Superglobals Plus
# Updated 2011/08/07 (lat9) to support ZC 1.5.0
# Updated 2012/11/23 (lat9) to add the SHOW_SUPERGLOBALS_QUERYCACHE configuration item and correct the configuration/configuration_group DELETEs
# 2007/10/15
# Use phpMyAdmin (add table prefixes if needed)
# Or use the Zen Cart sql tool (it has bugs but copy and paste *usually* works, the upload feature is NOT recommended!).
# remove Superglobals settings
# 2006/03/03

# BOF remove old Super Globals Plus configurations
#SET @superglobalsid=0; Don't want this for 1.5.0
SELECT (@superglobalsid:=configuration_group_id) AS superglobalsid
FROM configuration_group
WHERE configuration_group_title='Superglobals';
DELETE FROM configuration WHERE configuration_group_id=@superglobalsid AND configuration_group_id != 0;
DELETE FROM configuration_group WHERE configuration_group_id=@superglobalsid AND configuration_group_id != 0;
DELETE FROM admin_pages WHERE page_key='configSuperglobals';
# EOF remove old Super Globals Plus configurations

# Insert new Superglobals Configuration Group
SELECT @sortorder:=max(sort_order) FROM configuration_group;
INSERT INTO configuration_group VALUES (NULL, 'Superglobals', 'Superglobals Settings', @sortorder+1, '1');
SET @superglobalsid=LAST_INSERT_ID();
UPDATE configuration_group SET sort_order = @superglobalsid WHERE configuration_group_id = @superglobalsid;

# Insert Superglobals settings

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Enable Superglobals (catalog)', 'SHOW_SUPERGLOBALS', 'false', 'If true, the Superglobals will be shown in the shop at the bottom of the pages (and depending on the settings below).', @superglobalsid , 10, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Enable Superglobals (admin)', 'SHOW_SUPERGLOBALS_ADMIN', 'false', 'If true, the Superglobals will be shown in the admin at the top of pages (and depending on the settings below).', @superglobalsid , 15, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show Superglobals in Popup', 'SHOW_SUPERGLOBALS_POPUP', 'true', 'If true the Superglobals will be displayed in a popup window, using javascript. Set to false if you do not want or can not use javascript and the Superglobals will be displayed at the bottom of each page.', @superglobalsid , 18, NULL, 'zen_cfg_select_option(array(\'true\', \'false\'),');

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show the Superglobals to all (no ip check)', 'SHOW_SUPERGLOBALS_TO_ALL', 'false', 'If true, the Superglobals will be shown to every visitor (= Security risk).', @superglobalsid , 20, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'List of allowed ip addresses (comma separated list)', 'SHOW_SUPERGLOBALS_IP', '127.0.0.1', 'Enter a comma separated list of allowed ip addresses. Default setting: 127.0.0.1 (=localhost)', @superglobalsid , 30, NULL , NULL );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Maximum recursion level', 'SHOW_SUPERGLOBALS_MAX_LEVEL', '12', 'Enter the maximum recursion level. Default setting: 12. This prevents infinite loops in case of recursion ($GLOBALS recursion is automaticly detected). 0 disables the maximum recursion level protection', @superglobalsid , 32, NULL , NULL );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show $GLOBALS (shows all globals)', 'SHOW_SUPERGLOBALS_ALL', 'false', 'Show the contents of all Globals as well as all Super Globals. If enabled, the below settings for showing Super Globals will be overridden.', @superglobalsid , 34, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Show the "queryCache" object?', 'SHOW_SUPERGLOBALS_QUERYCACHE', 'false', 'If "Show $GLOBALS" is set to "true", show the contents of the queryCache object?. If enabled, the amount of time required to format the $GLOBALS will be impacted.', @superglobalsid , 35, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

INSERT INTO configuration ( configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function )
VALUES ( 'Filter HTTP_ variables', 'SHOW_SUPERGLOBALS_FILTER_HTTP', 'true', 'If true, the "deprecated" HTTP_ variables are filtered out (recommended).', @superglobalsid , 36, NULL , 'zen_cfg_select_option(array(\'true\', \'false\'),' );

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


# Register the page for Admin Access Control
INSERT INTO admin_pages (page_key,language_key,main_page,page_params,menu_key,display_on_menu,sort_order)
VALUES ('configSuperglobals','BOX_CONFIGURATION_SUPERGLOBALS','FILENAME_CONFIGURATION',CONCAT('gID=',@superglobalsid),'configuration','Y',@superglobalsid);