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

function convert_result2directory($resultset, $title, $paramstr, $page)
{
    global $output_limit;
    $outstr = "";
    $numrows = count($resultset);
    if ($numrows == 0) {
        throw new Exception('No Results');
        exit();
    }
    $outstr .= "<CiscoIPPhoneDirectory>\n";
    $outstr .= "<Title>$title</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    foreach($resultset as $row) {
        $outstr .= "<DirectoryEntry>";
        $outstr .= "<Name>" . $row['firstname'] . ' ' . $row['lastname'] . "</Name>";
        $outstr .= "<Telephone>" . $row['phonenumber'] . "</Telephone>";
        $outstr .= "</DirectoryEntry>\n";
    }

    if ($page != 0) {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>Prev</Name>";
        $outStr .= "<Position>1</Position>";
        $outStr .= "<URL>phonebook.php?" . htmlentities($paramstr) . "&amp;page=" . $page - 1 ."</URL>";
        $outstr .= "</SoftKeyItem>\n";
    }
    if ($numrows >= $output_limit){
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>Next</Name>";
        $outStr .= "<Position>2</Position>";
        $outStr .= "<URL>phonebook.php?" . htmlentities($paramstr) . "&amp;page=" . $page - 1 ."</URL>";
        $outstr .= "</SoftKeyItem>\n";
    }
    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Close</Name>";
    $outstr .= "<Position>4</Position>";
    $outstr .= "<URL>SoftKey:Exit</URL>";
    $outstr .= "</SoftKeyItem>\n";
    $outstr .= "</CiscoIPPhoneDirectory>\n";
    return $outstr;
}

function search_results($device='NONE', $searchname, $page=0, $order="ASC")
{
    global $searchQry, $output_limit;
    $DB = new MyPDO();
    $stmt = $DB->prepare($searchQry);				   		// use prepared query string
    $searchterm = "%$searchname%";
    $stmt->bindParam(":searchname", $searchterm, PDO::PARAM_STR);
    $stmt->bindValue(":offset", (int)($page * $output_limit), PDO::PARAM_INT);
    $stmt->bindValue(":max", (int)$output_limit, PDO::PARAM_INT);
#    $stmt->bindValue(":ordering", 'ASC');					// need to figure this out still
    
    $stmt->execute();
    $resultset = $stmt->fetchAll();
    
    $paramstr = "action=search&searchname=$searchname&order=$order&name=$device";
    $titlestr = "Search Directory for '$searchname'";
    print convert_result2directory($resultset, $titlestr, $paramstr, $page);
    $stmt=NULL;
    $DB = NULL;
}

function browse_company($device='NONE', $page=0, $order='ASC')
{
    global $companyQry, $output_limit; 
    $DB = new MyPDO();
    $stmt = $DB->prepare($companyQry);				   		// use prepared query string
    $stmt->bindValue(":offset", (int)($page * $output_limit), PDO::PARAM_INT);
    $stmt->bindValue(":max", (int)$output_limit, PDO::PARAM_INT);
    #$stmt->bindParam(':ordering', $order, PDO::PARAM_STR);
    
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