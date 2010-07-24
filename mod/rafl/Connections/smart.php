<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"

//require_once($_SERVER["DOCUMENT_ROOT"] . '/config.php');

//if (isset($CFG)) {
    $smart = mysql_pconnect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass) or die(mysql_error());
    mysql_select_db($CFG->dbname, $smart);
    mysql_query("SET SESSION character_set_results = 'UTF8'");
//}
?>