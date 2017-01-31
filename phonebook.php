<?php
require_once (dirname(__FILE__) . "/lib/header.php");
require_once (dirname(__FILE__) . "/conf/config.php");
require_once (dirname(__FILE__) . "/lib/DBConnect.php");

function main_menu($device='NONE')
{
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Please select one</Prompt>
        <MenuItem>
                <Name>Search Company Directory</Name>
                <URL>phonebook.php?action=search&amp;device=$device</URL>
        </MenuItem>
        <MenuItem>
                <Name>View Company Directory</Name>
                <URL>phonebook.php?action=company&amp;device=$device</URL>
        </MenuItem>
        <MenuItem>
                <Name>View Directory Backwards</Name>
                <URL>phonebook.php?action=company&amp;order=reverse&amp;device=$device</URL>
        </MenuItem>
</CiscoIPPhoneMenu>\n";
    print($outstr);
}

function search_menu($device='NONE')
{
    $outstr = 
"<CiscoIPPhoneInput>
        <Prompt>Enter first letters to search</Prompt>
        <URL>phonebook.php?action=search</URL>
        <InputItem>
                <DisplayName>Name</DisplayName>
                <QueryStringParam>searchname</QueryStringParam>
                <DefaultValue></DefaultValue>
                <InputFlags>A</InputFlags>
        </InputItem>
</CiscoIPPhoneInput>\n";
    print($outstr);
}

function handle_error($error='', $device='NONE')
{
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Error Occurred: " . $error . "</Prompt>
        <URL>phonebook.php?name=$device</URL>
</CiscoIPPhoneMenu>\n";
    print($outstr);
}

function convert_result2directory($resultset, $qrystr, $page)
{
    $outStr = "";
    $numrows = mysqli_num_rows($resultset);
    if ($numrows == 0) {
        handle_error('No Results', $device);
        exit();
    }
    $outstr .= "<CiscoIPPhoneDirectory>\n";
    $outstr .= "<Title>Search Directory for '$searchname'</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    while ($row = mysqli_fetch_assoc($resultset)) {
        $outstr .= "<DirectoryEntry>";
        $outstr .= "  <Name>" . $row['name'] . "</Name>";
        $outstr .= "  <Telephone>" . $row['phonenumber'] . "</Telephone>";
        $outstr .= "</DirectoryEntry>";
    }
    $stmt->close();

    if ($page != 0) {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "  <Name>Prev</Name>";
        $outStr .= "  <Position>1</Position>";
        $outStr .= "  <URL>phonebook.php?" . htmlentities($qrystr) . "&amp;page=" . $page - 1 ."</URL>";
        $outstr .= "</SoftKeyItem>";
    }
    if ($numrows >= $limit){
        $outstr .= "<SoftKeyItem>";
        $outstr .= "  <Name>Next</Name>";
        $outStr .= "  <Position>2</Position>";
        $outStr .= "  <URL>phonebook.php?" . htmlentities($qrystr) . "&amp;page=" . $page - 1 ."</URL>";
        $outstr .= "</SoftKeyItem>";
    }
    $outstr .= "<SoftKeyItem>";
    $outstr .= "  <Name>Close</Name>";
    $outStr .= "  <Position>4</Position>";
    $outStr .= "  <URL>SoftKey:Exit</URL>";
    $outstr .= "</SoftKeyItem>";
    $outstr .= "</CiscoIPPhoneDirectory>";
    return $outStr;
}

function search_results($device='NONE', $searchname='', $page=0, $order='ASC')
{
    global $dbhost, $dbuser, $dbpass, $dbname, $email, $debug;
    try {
        $DB = db_connect($dbhost, $dbuser, $dbpass, $dbname, $email, $debug);
        if (!$DB) {
            handle_error('Could not connect to DB', $device);
            exit();
        }
        // prevent injection
        $searchname = mysqli_real_escape_string($searchname);
        $page = mysqli_real_escape_string($page);
        $order = mysqli_real_escape_string($order);
        
        $stmt = $DB->prepare($searchQry);				   		// use prepared query string
        $stmt->bind_param($searchname, $order, ($page * $limit), $limit);		// insert variables
        $qrystr = "action=search&searchname=$searchname&order=$order&name=$device";
        $resultset = $stmt->execute();
        
        print convert_result2directory($resultset, $qrystr, $page);
    } catch (Exception $e) {
        handle_error($e->getMessage(), $device);
    }
}

function browse_company($device='NONE', $page=0, $order='ASC')
{
    global $dbhost, $dbuser, $dbpass, $dbname, $email, $debug;
    try {
        $DB = db_connect($dbhost, $dbuser, $dbpass, $dbname, $email, $debug);
        if (!$DB) {
            handle_error('Could not connect to DB', $device);
            exit();
        }
        // prevent injection
        $searchname = mysqli_real_escape_string($searchname);
        $page = mysqli_real_escape_string($page);
        $order = mysqli_real_escape_string($order);
        
        $stmt = $DB->prepare($companyQry);				   		// use prepared query string
        $stmt->bind_param($order, ($page * $limit), $limit);			// insert variables
        $qrystr = "action=company&searchname=$searchname&order=$order&name=$device";
        $resultset = $stmt->execute();

        print convert_result2directory($resultset, $qrystr, $page);
    } catch (Exception $e) {
        handle_error($e->getMessage(), $device);
    }
}

// MAIN
#$action = @$_REQUEST['action'] ?: 'mainmenu';
$action = @$_REQUEST['action'] ?: 'search';
$locale = @$_REQUEST['locale'] ?: 'English_United_States';
$device = @$_REQUEST['name'] ?: 'NONE';
switch($action) {
    case "search":
        #$searchname = @$_REQUEST['searchname'] ?: 'NONE';
        $searchname = @$_REQUEST['searchname'] ?: 'diederik';
        $page = @$_REQUEST['page'] ?: 0;
        $order = @$_REQUEST['order'] ?: 'ASC';
        if ($searchname == "NONE") {
            search_menu($device);
        } else {
            search_results($device, $searchname, $page, $order);
        }
        break;

    case "company":
        $page = @$_REQUEST['page'] ?: 0;
        $order = @$_REQUEST['order'] ?: 'ASC';
        browse_company($device, $page, $order);
        break;
        
    case "mainmenu":
    default:
        main_menu($device);
}
?>