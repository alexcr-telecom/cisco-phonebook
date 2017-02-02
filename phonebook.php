<?php
require_once (dirname(__FILE__) . "/lib/header.php");
require_once (dirname(__FILE__) . "/conf/config.php");
require_once (dirname(__FILE__) . "/lib/pdo.php");

function main_menu($device='NONE')
{
    $baseurl = get_baseurl();
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Please select one</Prompt>
        <MenuItem>
                <Name>Search Company Directory</Name>
                <URL>$baseurl?action=search&amp;device=$device</URL>
        </MenuItem>
        <MenuItem>
                <Name>View Company Directory</Name>
                <URL>$baseurl?action=company&amp;device=$device</URL>
        </MenuItem>
        <MenuItem>
                <Name>View Directory Backwards</Name>
                <URL>$baseurl?action=company&amp;order=reverse&amp;device=$device</URL>
        </MenuItem>
</CiscoIPPhoneMenu>\n";
    print($outstr);
}

function search_menu($device='NONE')
{
    $baseurl = get_baseurl();
    $outstr = 
"<CiscoIPPhoneInput>
        <Prompt>Enter first letters to search</Prompt>
        <URL>$baseurl?action=search</URL>
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
    $path = get_baseurl();
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

    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Dial</Name>";
    $outstr .= "<Position>1</Position>";
    $outstr .= "<URL>SoftKey:Dial</URL>";
    $outstr .= "</SoftKeyItem>\n";

    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>EditDial</Name>";
    $outstr .= "<URL>SoftKey:EditDial</URL>";
    $outstr .= "<Position>2</Position>";
    $outstr .= "</SoftKeyItem>";

    if ($page != 0) {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>Prev</Name>";
        $outstr .= "<Position>2</Position>";
        $outstr .= "<URL>$baseurl?" . htmlentities($paramstr) . "&amp;page=" . $page - 1 ."</URL>";
        $outstr .= "</SoftKeyItem>\n";
    }
 
    if ($numrows >= $output_limit){
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>Next</Name>";
        $outstr .= "<Position>3</Position>";
        $outstr .= "<URL>$baseurl?" . htmlentities($paramstr) . "&amp;page=" . $page - 1 ."</URL>";
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

function search_results($device, $searchname, $page, $orderBy, $order)
{
    global $searchQry, $output_limit;
    $DB = new MyPDO();
    $stmt = $DB->prepare($searchQry);				   		// use prepared query string
    $searchterm = "%$searchname%";
    $stmt->bindParam(":searchname", $searchterm, PDO::PARAM_STR);
    $stmt->bindParam(":orderBy", $orderBy, PDO::PARAM_STR);
    $stmt->bindValue(":offset", (int)($page * $output_limit), PDO::PARAM_INT);
    $stmt->bindValue(":max", (int)$output_limit, PDO::PARAM_INT);
#    $stmt->bindValue(":ordering", 'ASC');					// need to figure this out still
    
    $stmt->execute();
    $resultset = $stmt->fetchAll();
    
    $paramstr = htmlentities("action=search&searchname=$searchname&orderBy=$orderBy&order=$order&name=$device");
    $titlestr = "Search Directory for '$searchname'";
    print convert_result2directory($resultset, $titlestr, $paramstr, $page);
    $stmt=NULL;
    $DB = NULL;
}

function convert_result2menu($resultset, $title, $paramstr, $page, $block)
{
    global $output_limit;
    $baseurl = get_baseurl();
    $outstr = "";
    $numrows = count($resultset);
    if ($numrows == 0) {
        throw new Exception('No Results');
        exit();
    }
    $outstr .= "<CiscoIPPhoneMenu>\n";
    $outstr .= "<Title>$title</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    if ($page > 0) {
        $outstr .= "<MenuItem>";
        $outstr .= "<Name>Previous</Name>";
        $outstr .= "<URL>$baseurl?action=company&amp;$paramstr&amp;block=$block;page=" . $page - 1 ."</URL>";
        $outstr .= "</MenuItem>\n";
    }
    foreach($resultset as $row) {
        $outstr .= "<MenuItem>";
        $outstr .= "<Name>" . $row['firstname'] . ' ' . $row['lastname'] . "</Name>";
        $outstr .= "<URL>QueryStringParam:id=" . $row['id'] . "</URL>";
        $outstr .= "</MenuItem>\n";
    }
    if ($numrows >= $output_limit){
        $outstr .= "<MenuItem>";
        $outstr .= "<Name>Next</Name>";
        $outstr .= "<URL>$baseurl?action=company&$paramstr&amp;block=$block;page=" . $page + 1 ."</URL>";
        $outstr .= "</MenuItem>\n";
    }

    $pos = 1;
    if ($block != 'AG') {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>[A-G]</Name>";
        $outstr .= "<Position>$pos</Position>";
        $outstr .= "<URL>$baseurl?action=company&amp;$paramstr&amp;block=AG</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
    if ($block != 'HO') {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>[H-O]</Name>";
        $outstr .= "<Position>$pos</Position>";
        $outstr .= "<URL>$baseurl?action=company&amp;$paramstr&amp;block=HO</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
    if ($block != 'PZ') {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>[P-Z]</Name>";
        $outstr .= "<Position>$pos</Position>";
        $outstr .= "<URL>$baseurl?action=company&amp;$paramstr&amp;block=PZ</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
    
    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Choose</Name>";
    $outstr .= "<Position>$pos</Position>";
        $outstr .= "<URL>$baseurl?action=search&amp;$paramstr</URL>";
    $outstr .= "</SoftKeyItem>\n";
    $pos++;
    
    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Close</Name>";
    $outstr .= "<Position>$pos</Position>";
    $outstr .= "<URL>SoftKey:Exit</URL>";
    $outstr .= "</SoftKeyItem>\n";
    
    $outstr .= "</CiscoIPPhoneMenu>\n";
    return $outstr;
}

function browse_company($device, $page, $orderBy, $order, $block)
{
    global $companyQry, $output_limit; 
    $DB = new MyPDO();
    $stmt = $DB->prepare($companyQry);				   		// use prepared query string
    $stmt->bindParam(":orderBy", $orderBy, PDO::PARAM_STR);
    $stmt->bindValue(":offset", (int)($page * $output_limit), PDO::PARAM_INT);
    $stmt->bindValue(":max", (int)$output_limit, PDO::PARAM_INT);
    #$stmt->bindParam(':ordering', $order, PDO::PARAM_STR);
    
    $stmt->execute();
    $resultset = $stmt->fetchAll();

    $paramstr = htmlentities("orderBy=$orderBy&order=$order&name=$device");
    $title = "Company Directory";
    print convert_result2menu($resultset, $title, $paramstr, $page, $block);
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
        $orderBy = @$_REQUEST['orderBy'] ?: 'lastname';
        $order = @$_REQUEST['order'] ?: 'ASC';
        if ($searchname == "NONE") {
            search_menu($device);
        } else {
            search_results($device, $searchname, $page, $orderBy, $order);
        }
        break;

    case "company":
        $page = @$_REQUEST['page'] ?: 0;
        $orderBy = @$_REQUEST['orderBy'] ?: 'lastname';
        $order = @$_REQUEST['order'] ?: 'ASC';
        $block = @$_REQUEST['block'] ?: 'AG';
        browse_company($device, $page, $orderBy, $order, $block);
        break;
        
    case "mainmenu":
    default:
        main_menu($device);
}
?>