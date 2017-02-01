<?php
require_once (dirname(__FILE__) . "/lib/header.php");
require_once (dirname(__FILE__) . "/conf/config.php");
require_once (dirname(__FILE__) . "/lib/pdo.php");

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

function convert_result2directory($resultset, $paramstr, $page)
{
    $outStr = "";
    $numrows = count($resultset);
    if ($numrows == 0) {
        throw new Exception('No Results');
        exit();
    }
    $outstr .= "<CiscoIPPhoneDirectory>\n";
    $outstr .= "<Title>Search Directory for '$searchname'</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    foreach($resultset as $row) {
        $outstr .= "<DirectoryEntry>";
        $outstr .= "  <Name>" . $row['name'] . "</Name>";
        $outstr .= "  <Telephone>" . $row['phonenumber'] . "</Telephone>";
        $outstr .= "</DirectoryEntry>";
    }

    if ($page != 0) {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "  <Name>Prev</Name>";
        $outStr .= "  <Position>1</Position>";
        $outStr .= "  <URL>phonebook.php?" . htmlentities($paramstr) . "&amp;page=" . $page - 1 ."</URL>";
        $outstr .= "</SoftKeyItem>";
    }
    if ($numrows >= $limit){
        $outstr .= "<SoftKeyItem>";
        $outstr .= "  <Name>Next</Name>";
        $outStr .= "  <Position>2</Position>";
        $outStr .= "  <URL>phonebook.php?" . htmlentities($paramstr) . "&amp;page=" . $page - 1 ."</URL>";
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

function search_results($device='NONE', $searchname, $page=0, $order='ASC')
{
    global $searchQry, $output_limit;
    $DB = db_connect();
    $stmt = $DB->prepare($searchQry);				   		// use prepared query string
    
    $stmt->bindParam(':searchname', $searchname, PDO::PARAM_STR);
    $stmt->bindParam(':ordering', $order, PDO::PARAM_STR);
    #$stmt->bindParam(':offset', ($page * $output_limit), PDO::PARAM_INT);
    $stmt->bindParam(':max', $output_limit, PDO::PARAM_INT);
    
    $stmt->execute();
    $resultset = $stmt->fetchAll();
    
    $paramstr = "action=search&searchname=$searchname&order=$order&name=$device";
    print convert_result2directory($resultset, $paramstr, $page);
    $stmt=NULL;
    $DB = NULL;
}

function browse_company($device='NONE', $page=0, $order='ASC')
{
    global $companyQry, $output_limit; 
    $DB = db_connect();
    $stmt = $DB->prepare($companyQry);				   		// use prepared query string
    
    $stmt->bindParam(':ordering', $order, PDO::PARAM_STR);
    #$stmt->bindParam(':offset', ($page * $output_limit), PDO::PARAM_INT);
    $stmt->bindParam(':max', $output_limit, PDO::PARAM_INT);
    
    $stmt->execute();
    $resultset = $stmt->fetchAll();

    $paramstr = "action=company&searchname=$searchname&order=$order&name=$device";
    print convert_result2directory($resultset, $paramstr, $page);
    $stmt=NULL;
    $DB = NULL;
}

// MAIN
$action = @$_REQUEST['action'] ?: $default_action;
$locale = @$_REQUEST['locale'] ?: 'English_United_States';
$device = @$_REQUEST['name'] ?: 'NONE';
switch($action) {
    case "search":
        $searchname = @$_REQUEST['searchname'] ?: $default_searchname;
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