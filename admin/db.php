<?php
require_once("dbauth.php");
$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die(mysql_error());
mysql_select_db(DB_NAME_2, $db);

function html_sanitize($string)
{
	return(htmlentities($string));
}
