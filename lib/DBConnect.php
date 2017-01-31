<?php
require_once (dirname(__FILE__) . "/../conf/config.php");

function db_connect($HostName="localhost", $UserName="root", $PassWord="", $DBName="asterisk", $SendErrorEmailTo="root@localhost", $Debug=0)
{
	$DB = new mysqli($HostName, $UserName, $PassWord, $DBName);
	if (mysqli_connect_errno()) {
		$message="Can not connect to mysql database $DBName on $HostName as user $UserName";
		error_log("$message", 0);
		if ($SendErrorEmailTo != "") {
			error_log("$message", 1, $SendErrorEmailTo);
		}
		throw new Exception('Can not connect to database');
	}
	print($DB->character_set_name());
	$DB->set_charset("utf8");
	return $DB;
}
?>
