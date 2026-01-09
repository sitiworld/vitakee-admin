
DELIMITER //

CREATE TRIGGER trg_test_images_update
AFTER UPDATE ON test_images
FOR EACH ROW
BEGIN
  DECLARE change_data TEXT;

  IF OLD.id_test_panel <> NEW.id_test_panel THEN
    SET change_data = JSON_OBJECT(
      'id_test_panel', JSON_OBJECT('old', OLD.id_test_panel, 'new', NEW.id_test_panel)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_images', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.id_test <> NEW.id_test THEN
    SET change_data = JSON_OBJECT(
      'id_test', JSON_OBJECT('old', OLD.id_test, 'new', NEW.id_test)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_images', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.name_image <> NEW.name_image THEN
    SET change_data = JSON_OBJECT(
      'name_image', JSON_OBJECT('old', OLD.name_image, 'new', NEW.name_image)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_images', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.description <> NEW.description THEN
    SET change_data = JSON_OBJECT(
      'description', JSON_OBJECT('old', OLD.description, 'new', NEW.description)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'test_images', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END;
//

CREATE TRIGGER trg_test_images_delete
BEFORE DELETE ON test_images
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname
  ) VALUES (
    'test_images', OLD.id, 'DELETE_PHYSICAL', @user_id,
    NULL, JSON_OBJECT(
      'id_test_panel', OLD.id_test_panel,
      'id_test', OLD.id_test,
      'name_image', OLD.name_image,
      'description', OLD.description
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname
  );
END;
//

CREATE TRIGGER trg_test_images_delete_logical
AFTER UPDATE ON test_images
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
      'test_images', OLD.id, 'DELETE_LOGICAL', @user_id,
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
