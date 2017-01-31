<?php
require_once "header.php";
require_once "DBConnect.php";

function main_menu($device='NONE')
{
?>
<CiscoIPPhoneMenu>
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
</CiscoIPPhoneMenu>
<?php
}

function search_menu($device='NONE')
{
?>
<CiscoIPPhoneInput>
        <Prompt>Enter first letters to search</Prompt>
        <URL>phonebook.php?action=search</URL>
        <InputItem>
                <DisplayName>Name</DisplayName>
                <QueryStringParam>searchname</QueryStringParam>
                <DefaultValue></DefaultValue>
                <InputFlags>A</InputFlags>
        </InputItem>
</CiscoIPPhoneInput>"
<?php
}

function handle_error($device='NONE')
{
?>
<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Error Occurred</Prompt>
        <URL>phonebook.php</URL>
</CiscoIPPhoneMenu>
<?php
}

function search_results($device='NONE', $searchname='', $page=0, $order='ASC')
{
    $outStr = "";
    $DB = db_connect($dbhost, $dbuser, $dbpass, $dbname, $email, $debug);
    if (!$DB) {
        handle_error();
        exit();
    }
    // prevent injection
    $searchname = mysql_real_escape_string($searchname);
    $page = mysql_real_escape_string($page);
    $order = mysql_real_escape_string($order);
    
    $stmt = $DB->prepare($searchQry);				   // use prepared query string
    $stmt->bind_param($searchname, $order, $page, $limit);	   // insert variables
    $resultset = $stmt->execute();
    $numrows = mysql_num_rows($resultset);
    if ($numrows == 0) {
        echo "No rows found, nothing to print so am exiting";
        handle_error();
        exit;
    }
    $outstr .= "<CiscoIPPhoneDirectory>\n";
    $outstr .= "<Title>Search Directory for '$searchname'</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    while ($row = mysql_fetch_assoc($result)) {
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
        $outStr .= "  <URL>phonebook.php?action=search&amp;searchname=" . htmlentities($searchname) . "%amp;order=" . $order . "&amp;page=" . $page ."</URL>";
        $outstr .= "</SoftKeyItem>";
    }
    if ($numrows == $limit){
        $outstr .= "<SoftKeyItem>";
        $outstr .= "  <Name>Next</Name>";
        $outStr .= "  <Position>2</Position>";
        $outStr .= "  <URL>phonebook.php?action=search&amp;searchname=" . htmlentities($searchname) . "%amp;order=" . $order . "&amp;page=" . $page ."</URL>";
        $outstr .= "</SoftKeyItem>";
    }
    $outstr .= "<SoftKeyItem>";
    $outstr .= "  <Name>Close</Name>";
    $outStr .= "  <Position>4</Position>";
    $outStr .= "  <URL>SoftKey:Exit</URL>";
    $outstr .= "</SoftKeyItem>";
    $outstr .= "</CiscoIPPhoneDirectory>";

    print $outStr;
}

function browse_company($device='NONE', $page=0, $order='ascending')
{
    $DB = db_connect($dbhost, $dbuser, $dbpass, $email, $debug);
    if (!$DB) {
        handle_error();
    }
}

// MAIN
$action = @$_REQUEST['action'] ?: 'mainmenu';
$locale = @$_REQUEST['locale'] ?: 'English_United_States';
$device = @$_REQUEST['name'] ?: 'NONE';
switch($action) {
    case "search":
        $searchname = @$_REQUEST['searchname'] ?: 'NONE';
        $searchname = @$_REQUEST['page'] ?: 0;
        $searchname = @$_REQUEST['order'] ?: 'ASC';
        if ($searchname == "NONE") {
            search_menu($device);
        } else {
            search_results($device, $searchname, $page, $order);
        }
        break;

    case "company":
        $searchname = @$_REQUEST['page'] ?: 0;
        $searchname = @$_REQUEST['order'] ?: 'ASC';
        browse_company($device, $page, $order);
        break;
        
    case "mainmenu":
    default:
        main_menu($device);
}
?>