
DELIMITER //

CREATE TRIGGER trg_backups_update
AFTER UPDATE ON backups
FOR EACH ROW
BEGIN
  DECLARE change_data TEXT;

  IF OLD.name <> NEW.name THEN
    SET change_data = JSON_OBJECT(
      'name', JSON_OBJECT('old', OLD.name, 'new', NEW.name)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'backups', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.date <> NEW.date THEN
    SET change_data = JSON_OBJECT(
      'date', JSON_OBJECT('old', OLD.date, 'new', NEW.date)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'backups', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END;
//

CREATE TRIGGER trg_backups_delete
BEFORE DELETE ON backups
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname
  ) VALUES (
    'backups', OLD.id, 'DELETE_PHYSICAL', @user_id,
    NULL, JSON_OBJECT(
      'name', OLD.name,
      'date', OLD.date
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname
  );
END;
//

CREATE TRIGGER trg_backups_delete_logical
AFTER UPDATE ON backups
FOR EACH ROW
BEGIN
  IF NEW.deleted_at IS NOT NULL AND OLD.deleted_at IS NULL THEN
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'backups', OLD.id, 'DELETE_LOGICAL', @user_id,
      JSON_OBJECT('deleted_at', JSON_OBJECT('old', NULL, 'new', NEW.deleted_at)),
      NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END;
//

DELIMITER ;
