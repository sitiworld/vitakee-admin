
DELIMITER //

CREATE TRIGGER trg_lipid_profile_record_update
AFTER UPDATE ON lipid_profile_record
FOR EACH ROW
BEGIN
  DECLARE change_data TEXT;

  IF OLD.user_id <> NEW.user_id THEN
    SET change_data = JSON_OBJECT(
      'user_id', JSON_OBJECT('old', OLD.user_id, 'new', NEW.user_id)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.lipid_profile_date <> NEW.lipid_profile_date THEN
    SET change_data = JSON_OBJECT(
      'lipid_profile_date', JSON_OBJECT('old', OLD.lipid_profile_date, 'new', NEW.lipid_profile_date)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.lipid_profile_time <> NEW.lipid_profile_time THEN
    SET change_data = JSON_OBJECT(
      'lipid_profile_time', JSON_OBJECT('old', OLD.lipid_profile_time, 'new', NEW.lipid_profile_time)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.ldl <> NEW.ldl THEN
    SET change_data = JSON_OBJECT(
      'ldl', JSON_OBJECT('old', OLD.ldl, 'new', NEW.ldl)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.hdl <> NEW.hdl THEN
    SET change_data = JSON_OBJECT(
      'hdl', JSON_OBJECT('old', OLD.hdl, 'new', NEW.hdl)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.total_cholesterol <> NEW.total_cholesterol THEN
    SET change_data = JSON_OBJECT(
      'total_cholesterol', JSON_OBJECT('old', OLD.total_cholesterol, 'new', NEW.total_cholesterol)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.triglycerides <> NEW.triglycerides THEN
    SET change_data = JSON_OBJECT(
      'triglycerides', JSON_OBJECT('old', OLD.triglycerides, 'new', NEW.triglycerides)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
  IF OLD.non_hdl <> NEW.non_hdl THEN
    SET change_data = JSON_OBJECT(
      'non_hdl', JSON_OBJECT('old', OLD.non_hdl, 'new', NEW.non_hdl)
    );
    INSERT INTO audit_log (
      table_name, record_id, action_type, action_by,
      changes, full_row,
      client_ip, client_hostname, user_agent,
      client_os, client_browser,
      domain_name, request_uri, server_hostname
    ) VALUES (
      'lipid_profile_record', OLD.id, 'UPDATE', @user_id,
      change_data, NULL,
      @client_ip, @client_hostname, @user_agent,
      @client_os, @client_browser,
      @domain_name, @request_uri, @server_hostname
    );
  END IF;
END;
//

CREATE TRIGGER trg_lipid_profile_record_delete
BEFORE DELETE ON lipid_profile_record
FOR EACH ROW
BEGIN
  INSERT INTO audit_log (
    table_name, record_id, action_type, action_by,
    changes, full_row,
    client_ip, client_hostname, user_agent,
    client_os, client_browser,
    domain_name, request_uri, server_hostname
  ) VALUES (
    'lipid_profile_record', OLD.id, 'DELETE_PHYSICAL', @user_id,
    NULL, JSON_OBJECT(
      'user_id', OLD.user_id,
      'lipid_profile_date', OLD.lipid_profile_date,
      'lipid_profile_time', OLD.lipid_profile_time,
      'ldl', OLD.ldl,
      'hdl', OLD.hdl,
      'total_cholesterol', OLD.total_cholesterol,
      'triglycerides', OLD.triglycerides,
      'non_hdl', OLD.non_hdl
    ),
    @client_ip, @client_hostname, @user_agent,
    @client_os, @client_browser,
    @domain_name, @request_uri, @server_hostname
  );
END;
//

CREATE TRIGGER trg_lipid_profile_record_delete_logical
AFTER UPDATE ON lipid_profile_record
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
      'lipid_profile_record', OLD.id, 'DELETE_LOGICAL', @user_id,
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
