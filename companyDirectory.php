<?php
# This page will create the company directory service. It has two actions. The first is default which
# will list the employee with extensions, the second lists the specific

# Copyright 2003 by Brandon Tatton and Jared Smith
# modified 2013 by Thomas Rechberger 
# Licensed under the GPL

##########################################
# Config section
# Please note: on your Apache you may need to set Addtype text/xml .xml
$xmldir = "http://192.168.0.200/CiscoDirectory";   # don't add a trailing slash!
$dialprefix = ""; #prefix for dialing out (if there is any)
$searchumlaut = "yes"; #cisco phone cannot display umlauts, if this option is enabled then you will be able to search for stored umlauts in mysql db
$mysqltable = "phonebook";
$argnumber = "number";
$argname = "name";
$argcontact = "contact";
$argmobile = "mobile";
$arghomenumber = "homenumber";
$argintern = "intern"; # use value of 1 in record if internal extension

#remove all (utf8) umlauts for displaying in xml because cisco sip phone cannot display ü,ä,ö
function removeumlauts($text) { 
   $search  = array ('&', 'Ä', 'Ö', 'Ü', 'ä',  'ö',  'ü',  'ß', 'Ã¤', 'Ã¶', 'Ã¼', 'Ã', 'Ã', 'Ã'); 
   $replace = array ('&amp;', 'Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue', 'ss', 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue'); 
   $str  = str_replace($search, $replace, $text); 
   return $str; 
} 

#convert to umlauts for searches on the phone (if names in mysql are stored with umlauts), this will cause an additional search
function converttoumlauts($text) { 
   $search = array ('Ae', 'Oe', 'Ue', 'ae', 'oe', 'ue'); 
   $Replace  = array ('Ä', 'Ö', 'Ü', 'ä',  'ö',  'ü'); 
   $str  = str_replace($search, $replace, $text); 
   return $str; 
} 


   
##########################################
#
# Set content type
header("Content-Type: text/xml; charset=utf-8");

# This is the include file that establishes the connection to the MySQL database
require ('DBConnect.php'); 

if ($searchumlaut == "yes") {
$getnameconverted = converttoumlauts($_GET['searchname']); #search with umlauts
}
$getname = $_GET['searchname'];

$getnumber = $_GET['displaynumber'];
$getdesc = $_GET['desc'];
$NextSet = $_GET['NextSet'];


# This sets the offset for the LIMIT portion of the query
$NextStartingRow = $NextSet*30;

# Connect to the database
$ConnectionSuccess = db_connect();
if (!$ConnectionSuccess) exit;

# If the variable "number" is passed in through the GET string (from below in script), then display 
# name, phone number, cell phone and home number for that record with the dial key functionality

if ($getnumber) {
	$DirectoryListing = "<CiscoIPPhoneDirectory>\n"; 
        
	$Query  = "SELECT $argnumber, $argname, $argcontact, $argmobile, $arghomenumber, $argintern ";
	$Query .= "FROM $mysqltable WHERE $argnumber IS NOT NULL AND $argnumber = '$getnumber' ";
	$Query .= "ORDER BY $argname";
	
	$SelectDirectoryInfo = mysql_query($Query,$ConnectionSuccess);

	while ($row = mysql_fetch_array($SelectDirectoryInfo)) {
		#$CellPhone = ereg_replace("[ ()-]+", "", $row['mobile']); #remove chars in number
		#$HomePhone = ereg_replace("[ ()-]+", "", $row['homenumber']);

		if ($row[$argintern] == 1) {
			$isintern = "yes";
		} else {
			$isintern = "no";
		}
		
		$DirectoryListing .= "<DirectoryEntry>\n";
		$DirectoryListing .= "<Name>" . removeumlauts($row[$argname]).", ".removeumlauts($row[$argcontact]) . "</Name>\n";
		if ($isintern == "yes") {
			$DirectoryListing .= "<Telephone>" . $row[$argnumber] . "</Telephone>\n";
		} else {
			$DirectoryListing .= "<Telephone>" . $dialprefix.$row[$argnumber] . "</Telephone>\n";
		}
		$DirectoryListing .= "</DirectoryEntry>\n";

		if ($row[$argmobile]) {
			$DirectoryListing .= "<DirectoryEntry>\n";
			$DirectoryListing .= "<Name>Mobile:</Name>\n";
			if ($isintern == "yes") {
				$DirectoryListing .= "<Telephone>" . $row[$argmobile] . "</Telephone>\n";
			} else {
				$DirectoryListing .= "<Telephone>" . $dialprefix.$row[$argmobile] . "</Telephone>\n";
			}
			$DirectoryListing .= "</DirectoryEntry>\n";
		}

		if ($row[$arghomenumber]) {
			$DirectoryListing .= "<DirectoryEntry>\n";
			$DirectoryListing .= "<Name>Home:</Name>\n";
			if ($isintern == "yes") {
				$DirectoryListing .= "<Telephone>" . $row[$arghomenumber] . "</Telephone>\n";
			} else {
				$DirectoryListing .= "<Telephone>" . $dialprefix.$row[$arghomenumber] . "</Telephone>\n";
			}
			$DirectoryListing .= "</DirectoryEntry>\n";
		}
	}
	
	$DirectoryListing .= "</CiscoIPPhoneDirectory>\n"; 
	
	echo "$DirectoryListing";

# If the variable name is not passed in on the GET string, then do the 
# entire company directory, unless name is passed in. If so then we 
# will be using a LIKE filter in our SQL query
} else {
	$CompanyNameList = "<CiscoIPPhoneMenu>\n";
	$CompanyNameList .= "<Title>Company Directory</Title>\n";
	$CompanyNameList .= "<Prompt>Please select one</Prompt>\n";

	$Query = "SELECT $argnumber, $argname, $argcontact, $argmobile, $arghomenumber ";
	$Query .= "FROM $mysqltable WHERE ('$argnumber' IS NOT NULL OR '$argname' IS NOT NULL) ";
	
	# If we are searching by name (or contact) then add this filter to the query
	if ($getname) $Query .= "and ($argname like '$getname%' or $argcontact like '$getname%') "; #search like entered on phone
	if ($getnameconverted) $Query .= "or ($argname like '$getnameconverted%' or $argcontact like '$getnameconverted%') "; #search also for umlauts
	
	# display backwards?
	if ($getdesc == "true") {
 	$Query .= "order by $argname DESC ";
	} else {
	$Query .= "order by $argname ";
	}

	# If this is the first page of the company directory then we will display the first 30
	if (!$NextSet) {
		$Query .= "Limit 0,30"; 
	# Now for each subsiquent call we get the next 30 records.
	} else {
		$Query .= "Limit $NextStartingRow,30"; 
	}

	# Execute the query
	$SelectNameList = mysql_query($Query,$ConnectionSuccess);
	
	# Count the number of rows returned. This is important because if a full 30 are returned 
	# we will display a more option
	$NumberOfRows = mysql_num_rows($SelectNameList);
	
	if ($NumberOfRows >= 30) {
		$NextSetValue = $NextSet+1;	
	}

	# Parse through the query and set up the menu items.
	while ($row = mysql_fetch_array($SelectNameList)) {
		$CompanyNameList .= "<MenuItem>\n";
			$CompanyNameList .= "<Name>"; 
			$CompanyNameList .= removeumlauts($row[$argname]); 
			if ($row[$argcontact]) $CompanyNameList .= ", " . removeumlauts($row[$argcontact]); 		
			$CompanyNameList .= "</Name>\n";
			$CompanyNameList .= "<URL>";
				$CompanyNameList .= "$xmldir/companyDirectory.php?";
				$CompanyNameList .= "displaynumber="; 
				$CompanyNameList .= $row[$argnumber]; 
				$CompanyNameList .= "</URL>\n";
		$CompanyNameList .= "</MenuItem>\n";
	}
	
	# If we set NextSetValue above then we will display the more option. Which sets NextSet
	if ($NextSetValue) {
		$CompanyNameList .= "<MenuItem>\n";
			$CompanyNameList .= "<Name>MORE</Name>\n";
			$CompanyNameList .= "<URL>$xmldir/companyDirectory.php?NextSet=$NextSetValue</URL>\n";
		$CompanyNameList .= "</MenuItem>\n";
	}
	$CompanyNameList .= "</CiscoIPPhoneMenu>";
	echo "$CompanyNameList";
	
}
?>
