<?php
require_once (dirname(__FILE__) . "/conf/config.php");
require_once (dirname(__FILE__) . "/lib/lib.php");

function main_menu($devicelocale)
{
    global $schema;
    $baseurl = get_baseurl();
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Please select one</Prompt>
        <MenuItem>
                <Name>Search Company Directory</Name>
                <URL>" . htmlentities("$baseurl?action=search&$devicelocale") . "</URL>
        </MenuItem>
        <MenuItem>
                <Name>View Company Directory by Lastname</Name>
                <URL>" . htmlentities("$baseurl?action=company&searchBy=lastname&orderBy=lastname&$devicelocale") . "</URL>
        </MenuItem>
        <MenuItem>
                <Name>View Company Directory by Firstname</Name>
                <URL>" . htmlentities("$baseurl?action=company&searchBy=firstname&orderBy=firstname&$devicelocale") . "</URL>
        </MenuItem>
</CiscoIPPhoneMenu>\n";
    print($outstr);
}

function search_menu($devicelocale)
{
    global $schema;
    $baseurl = get_baseurl();
    $outstr = 
"<CiscoIPPhoneInput$schema>
        <Prompt>Enter first letters to search</Prompt>
        <URL>" . htmlentities("$baseurl?action=search&$devicelocale") . "</URL>
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
    global $schema;
    $baseurl = get_baseurl();
    $outstr = "";
    $numrows = count($resultset);
    if ($numrows == 0) {
        throw new Exception('No Results');
        exit();
    }
    $outstr .= "<CiscoIPPhoneDirectory$schema>\n";
    $outstr .= "<Title>$title</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    foreach($resultset as $row) {
        $outstr .= "<DirectoryEntry>";
        $outstr .= "<Name>" . $row['firstname'] . ' ' . $row['lastname'] . "</Name>";
        $outstr .= "<Telephone>" . $row['phonenumber'] . "</Telephone>";
        $outstr .= "</DirectoryEntry>\n";
    }

    $pos = 1;
    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Dial</Name>";
    $outstr .= "<Position>$pos</Position>";
    $outstr .= "<URL>SoftKey:Dial</URL>";
    $outstr .= "</SoftKeyItem>\n";
    $pos++;

    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>EditDial</Name>";
    $outstr .= "<URL>SoftKey:EditDial</URL>";
    $outstr .= "<Position>$pos</Position>";
    $outstr .= "</SoftKeyItem>";
    $pos++;

    if ($page != 0) {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>Prev</Name>";
        $outstr .= "<Position>$pos</Position>";
        $url = htmlentities("$baseurl?page=" . ($page - 1) . "&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
 
    if ($numrows >= CONFIG_OUTPUT_LIMIT){
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>Next</Name>";
        $outstr .= "<Position>$pos</Position>";
        $url = htmlentities("$baseurl?page=" . ($page + 1) . "&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }

    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Back</Name>";
    $outstr .= "<Position>$pos</Position>";
    $outstr .= "<URL>SoftKey:Exit</URL>";
    $outstr .= "</SoftKeyItem>\n";
    $pos++;

    $outstr .= "</CiscoIPPhoneDirectory>\n";
    return $outstr;
}

function search_results($searchname, $devicelocale)
{
    $searchBy = @$_REQUEST['searchBy'] ?: CONFIG_DEFAULT_SEARCHBY;
    $orderBy = @$_REQUEST['orderBy'] ?: CONFIG_DEFAULT_ORDERBY;
    $page = (int) @$_REQUEST['page'] ?: 0;

    $DB = new MyPDO();

    $searchQry = str_replace("{{searchBy}}", $searchBy, CONFIG_SEARCH_QUERY);	// replace a fieldname placeholder
    
    $stmt = $DB->prepare($searchQry);				   		// use prepared query string
    $searchterm = "%$searchname%";
    $stmt->bindValue(":searchname", $searchterm, PDO::PARAM_STR);
    $stmt->bindValue(":orderBy", $orderBy, PDO::PARAM_STR);
    $stmt->bindValue(":offset", (int)($page * CONFIG_OUTPUT_LIMIT), PDO::PARAM_INT);
    $stmt->bindValue(":max", (int)CONFIG_OUTPUT_LIMIT, PDO::PARAM_INT);
    
    $stmt->execute();
    $resultset = $stmt->fetchAll();
    
    $paramstr = "action=search&searchname=$searchname&orderBy=$orderBy&$devicelocale";
    $titlestr = "Search Directory for '$searchname'";
    print convert_result2directory($resultset, $titlestr, $paramstr, $page);
    $stmt=NULL;
    $DB = NULL;
}

function convert_result2menu($resultset, $title, $searchBy, $paramstr, $page = 0, $block)
{
    global $schema;
    $baseurl = get_baseurl();
    $outstr = "";
    $numrows = count($resultset);
    $outstr .= "<CiscoIPPhoneMenu$schema>\n";
    $outstr .= "<Title>$title</Title>\n";
    $outstr .= "<Prompt>Please select one</Prompt>\n";
    if ($page > 0) {
        $outstr .= "<MenuItem>";
        $outstr .= "<Name>&lt;&lt; Previous Page</Name>";
        $url = htmlentities("$baseurl?action=company&searchBy=$searchBy&block=$block&page=" . ($page - 1) . "&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</MenuItem>\n";
    }
    foreach($resultset as $row) {
        $outstr .= "<MenuItem>";
        $outstr .= "<Name>" . $row['firstname'] . ' ' . $row['lastname'] . "</Name>";
        $url = htmlentities("$baseurl?action=search&searchBy=id&searchname=" . $row['id'] . "&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</MenuItem>\n";
    }
    if ($numrows >= CONFIG_OUTPUT_LIMIT){
        $outstr .= "<MenuItem>";
        $outstr .= "<Name>Next Page &gt;&gt;</Name>";
        $url = htmlentities("$baseurl?action=company&searchBy=$searchBy&block=$block&page=" . ($page + 1) . "&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</MenuItem>\n";
    }

    $pos = 1;
    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Choose</Name>";
    $outstr .= "<Position>$pos</Position>";
    $outstr .= "<URL>QueryStringParam:button=submit</URL>";
    $outstr .= "</SoftKeyItem>\n";
    $pos++;

    if ($block != 'AG') {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>[A-G]</Name>";
        $outstr .= "<Position>$pos</Position>";
        $url = htmlentities("$baseurl?action=company&searchBy=$searchBy&block=AG&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
    if ($block != 'HO') {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>[H-O]</Name>";
        $outstr .= "<Position>$pos</Position>";
        $url = htmlentities("$baseurl?action=company&searchBy=$searchBy&block=HO&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
    if ($block != 'PZ') {
        $outstr .= "<SoftKeyItem>";
        $outstr .= "<Name>[P-Z]</Name>";
        $outstr .= "<Position>$pos</Position>";
        $url = htmlentities("$baseurl?action=company&searchBy=$searchBy&block=PZ&$paramstr");
        $outstr .= "<URL>$url</URL>";
        $outstr .= "</SoftKeyItem>\n";
        $pos++;
    }
    
    $outstr .= "<SoftKeyItem>";
    $outstr .= "<Name>Back</Name>";
    $outstr .= "<Position>$pos</Position>";
    $outstr .= "<URL>SoftKey:Exit</URL>";
    $outstr .= "</SoftKeyItem>\n";
    $pos++;

    $outstr .= "</CiscoIPPhoneMenu>\n";
    return $outstr;
}

function browse_company($devicelocale)
{
    $searchBy = @$_REQUEST['searchBy'] ?: CONFIG_DEFAULT_SEARCHBY;
    $orderBy = @$_REQUEST['orderBy'] ?: CONFIG_DEFAULT_ORDERBY;
    $block = @$_REQUEST['block'] ?: CONFIG_DEFAULT_BLOCK;
    $page = (int) @$_REQUEST['page'] ?: 0;
    
    $DB = new MyPDO();
    
    $firstletter=substr($block, 0, 1);
    $lastletter=substr($block, -1);
    
    $companyQry = str_replace("{{searchBy}}", $searchBy, CONFIG_COMPANY_QUERY);	// replace a fieldname placeholder
    $stmt = $DB->prepare($companyQry);				   		// use prepared query string
    $stmt->bindValue(":firstletter", $firstletter, PDO::PARAM_STR);
    $stmt->bindValue(":lastletter", $lastletter, PDO::PARAM_STR);
    $stmt->bindValue(":orderBy", $orderBy, PDO::PARAM_STR);
    $stmt->bindValue(":offset", (int)($page * CONFIG_OUTPUT_LIMIT), PDO::PARAM_INT);
    $stmt->bindValue(":max", (int)CONFIG_OUTPUT_LIMIT, PDO::PARAM_INT);
    $stmt->execute();

    $resultset = $stmt->fetchAll();

    $paramstr = "orderBy=$orderBy&$devicelocale";
    $title = "Company Directory by $orderBy";
    print convert_result2menu($resultset, $title, $searchBy, $paramstr, $page, $block);
    $stmt=NULL;
    $DB = NULL;
}

// MAIN
$action = @$_REQUEST['action'] ?: CONFIG_DEFAULT_ACTION;
$locale = @$_REQUEST['locale'] ?: 'English_United_States';
$device = @$_REQUEST['name'] ?: 'NONE';
$devicelocale = "name=$device&locale=$locale";
switch($action) {
    case "search":
        $searchname = @$_REQUEST['searchname'] ?: CONFIG_DEFAULT_SEARCHNAME;
        if (is_null($searchname) or $searchname=='') {
            search_menu($devicelocale);
        } else {
            search_results($searchname, $devicelocale);
        }
        break;

    case "company":
        browse_company($devicelocale);
        break;
        
    case "mainmenu":
    default:
        main_menu($devicelocale);
}
?>