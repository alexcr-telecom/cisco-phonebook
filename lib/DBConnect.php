<?php
require_once (dirname(__FILE__) . "/../conf/config.php");

function db_connect($HostName="localhost", $UserName="root", $PassWord="", $DBName="asterisk", $SendErrorEmailTo="root@localhost", $Debug=0)
{
	try {
		$DB = mysqli_connect($HostName, $UserName, $PassWord, $DBName);
		$DB->set_charset("utf8");
	} catch (Exception $e) {
		$message="Can not connect to mysql database $DBName on $HostName as user $UserName";
		error_log("$message", 0);
		if ($SendErrorEmailTo != "") {
			error_log("$message", 1, $SendErrorEmailTo);
		}
		exit();
	}
	return $DB;
}
?>
