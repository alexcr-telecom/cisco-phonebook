<?php
require_once "config.php";

#This function will connect to the database
function db_connect($HostName="localhost", $UserName="root", $PassWord="", $DBName="asterisk", $SendErrorEmailTo="root@localhost", $Debug=0) {

	### Try to connect to the db server if we fail send an email and break
	$MyConnection = mysql_connect($HostName, $UserName, $PassWord);
	if (!$MyConnection) {
		$SendErrorCommand = "echo Can not connect to mysql on $HostName as user $UserName | /bin/mail -s 'Error connecting to mysql' $SendErrorEmailTo";
		if ($Debug) echo "$SendErrorCommand\n";
		exec($SendErrorCommand,$SendEmailErrorArray,$ReturnValue);
		return FALSE;
	} 
	
	### Try to select the db if we fail send an email and break
	$MySelectDB = mysql_select_db($DBName,$MyConnection);
	if (!$MySelectDB) {
		$SendErrorCommand = "echo Can not connect to mysql db $DBName on $HostName as $UserName | /bin/mail -s 'Error using db $DBName' $SendErrorEmailTo";
		if ($Debug) echo "$SendErrorCommand\n";
		exec($SendErrorCommand,$SendEmailErrorArray,$ReturnValue);
		return FALSE;
	} else {
		return $MyConnection;
	}
}
?>
