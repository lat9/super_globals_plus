# remove Superglobals settings
# 2006/03/03

SELECT @superglobalsid:=configuration_group_id
FROM configuration_group WHERE configuration_group_title="Superglobals";

DELETE FROM configuration WHERE configuration_group_id=@superglobalsid;

DELETE FROM configuration_group WHERE configuration_group_id=@superglobalsid;
