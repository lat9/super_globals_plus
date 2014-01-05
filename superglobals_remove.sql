# BOF remove old Super Globals Plus configurations
#SET @superglobalsid=0; Don't want this for 1.5.0
SELECT (@superglobalsid:=configuration_group_id) AS superglobalsid
FROM configuration_group
WHERE configuration_group_title='Superglobals';
DELETE FROM configuration WHERE configuration_group_id=@superglobalsid;
DELETE FROM configuration_group WHERE configuration_group_id=@superglobalsid;
DELETE FROM admin_pages WHERE page_key='configSuperglobals';