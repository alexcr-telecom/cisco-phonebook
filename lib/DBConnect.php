<?php
require_once (dirname(__FILE__) . "/../conf/config.php");

#This function will connect to the database
function db_connect($HostName="localhost", $UserName="root", $PassWord="", $DBName="asterisk", $SendErrorEmailTo="root@localhost", $Debug=0) {

	### Try to connect to the db server if we fail send an email and break
	try {
		$MyConnection = mysqli_connect($HostName, $UserName, $PassWord, $DBName);
	} catch (Exception $e) {
		$subject="Can not connect to mysql database $DBName on $HostName as user $UserName";
		$message="Database Connect Error Occurred\nHostNamne: $HostName\nUsername: $UserName\nDatabse: $DBName\n";
		if ($SendErrorEmailTo != "") {
			try {
				mail($SendErrorEmailTo, $subject, $message);
			} catch (Exception $e) {
				print("Error occured while attempting to send email:" . $e->getMessage());
				exit();
			}
		}
		if ($Debug) echo "$message\n";
		exit();
	}
	return $MyConnection;
}
?>
