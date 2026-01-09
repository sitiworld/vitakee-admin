
DELIMITER //

CREATE TRIGGER trg_test_panels_update
AFTER UPDATE ON test_panels
FOR EACH ROW
BEGIN
  DECLARE change_data TEXT;

  IF OLD.panel_name <> NEW.panel_name THEN
    SET change_data = JSON_OBJECT(
      'panel_name', JSON_OBJECT('old', OLD.panel_name, 'new', NEW.panel_name)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_panels', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.display_name <> NEW.display_name THEN
    SET change_data = JSON_OBJECT(
      'display_name', JSON_OBJECT('old', OLD.display_name, 'new', NEW.display_name)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_panels', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.display_name_es <> NEW.display_name_es THEN
    SET change_data = JSON_OBJECT(
      'display_name_es', JSON_OBJECT('old', OLD.display_name_es, 'new', NEW.display_name_es)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_panels', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END;
//

CREATE TRIGGER trg_test_panels_delete
BEFORE DELETE ON test_panels
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname
  ) VALUES (
    'test_panels', OLD.id, 'DELETE_PHYSICAL', @user_id,
    NULL, JSON_OBJECT(
      'panel_name', OLD.panel_name,
      'display_name', OLD.display_name,
      'display_name_es', OLD.display_name_es
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname
  );
END;
//

CREATE TRIGGER trg_test_panels_delete_logical
AFTER UPDATE ON test_panels
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
      'test_panels', OLD.id, 'DELETE_LOGICAL', @user_id,
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
