<?php
// Array definitions
  $tNG_login_config = array();
  $tNG_login_config_session = array();
  $tNG_login_config_redirect_success  = array();
  $tNG_login_config_redirect_failed  = array();

// Start Variable definitions
  $tNG_debug_mode = "PRODUCTION";
  $tNG_debug_log_type = "";
  $tNG_debug_email_to = "you@yoursite.com";
  $tNG_debug_email_subject = "[BUG] The site went down";
  $tNG_debug_email_from = "webserver@yoursite.com";
  $tNG_email_host = "";
  $tNG_email_user = "";
  $tNG_email_port = "25";
  $tNG_email_password = "";
  $tNG_email_defaultFrom = "nobody@nobody.com";
  $tNG_login_config["connection"] = "smart";
  $tNG_login_config["table"] = "school";
  $tNG_login_config["pk_field"] = "sc_id";
  $tNG_login_config["pk_type"] = "NUMERIC_TYPE";
  $tNG_login_config["email_field"] = "sc_primary_email";
  $tNG_login_config["user_field"] = "sc_user";
  $tNG_login_config["password_field"] = "sc_password";
  $tNG_login_config["level_field"] = "";
  $tNG_login_config["level_type"] = "STRING_TYPE";
  $tNG_login_config["randomkey_field"] = "";
  $tNG_login_config["activation_field"] = "";
  $tNG_login_config["password_encrypt"] = "false";
  $tNG_login_config["autologin_expires"] = "30";
  $tNG_login_config["redirect_failed"] = "admin/index.php";
  $tNG_login_config["redirect_success"] = "admin/welcome.php";
  $tNG_login_config["login_page"] = "admin/index.php";
  $tNG_login_config_session["kt_login_id"] = "sc_id";
  $tNG_login_config_session["kt_login_user"] = "sc_user";
// End Variable definitions
?>