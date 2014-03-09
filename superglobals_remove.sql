SELECT @superglobalsid:=configuration_group_id FROM configuration_group WHERE configuration_group_title="Superglobals";
DELETE FROM configuration WHERE configuration_group_id=@superglobalsid AND @superglobalsid != 0;
DELETE FROM configuration_group WHERE configuration_group_id=@superglobalsid AND @superglobalsid != 0;
DELETE FROM admin_pages WHERE page_key='configSuperglobals';