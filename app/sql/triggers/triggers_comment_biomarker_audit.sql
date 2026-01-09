
DELIMITER //

CREATE TRIGGER trg_comment_biomarker_update
AFTER UPDATE ON comment_biomarker
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
      'comment_biomarker', OLD.id, 'UPDATE', @user_id,
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
      'comment_biomarker', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.id_biomarker <> NEW.id_biomarker THEN
    SET change_data = JSON_OBJECT(
      'id_biomarker', JSON_OBJECT('old', OLD.id_biomarker, 'new', NEW.id_biomarker)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'comment_biomarker', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.comment <> NEW.comment THEN
    SET change_data = JSON_OBJECT(
      'comment', JSON_OBJECT('old', OLD.comment, 'new', NEW.comment)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'comment_biomarker', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END;
//

CREATE TRIGGER trg_comment_biomarker_delete
BEFORE DELETE ON comment_biomarker
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname
  ) VALUES (
    'comment_biomarker', OLD.id, 'DELETE_PHYSICAL', @user_id,
    NULL, JSON_OBJECT(
      'id_test_panel', OLD.id_test_panel,
      'id_test', OLD.id_test,
      'id_biomarker', OLD.id_biomarker,
      'comment', OLD.comment
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname
  );
END;
//

CREATE TRIGGER trg_comment_biomarker_delete_logical
AFTER UPDATE ON comment_biomarker
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
      'comment_biomarker', OLD.id, 'DELETE_LOGICAL', @user_id,
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
