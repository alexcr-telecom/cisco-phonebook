<?php
require_once (dirname(__FILE__) . "/../conf/config.php");

function get_baseurl(){
    if(isset($_SERVER['HTTP_HOST'])) {
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    } else{
        return dirname(__DIR__) . "/phonebook.php";
    }
}

function exception_handler($exception) 
{
    global $debug, $email;
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Error Occurred: " . $exception->getMessage() . "</Prompt>
</CiscoIPPhoneMenu>\n";
    print($outstr);

    if ($email) {
        error_log($exception->getMessage(), 1, $email);
    } else {
        error_log($exception->getMessage(), 0);
    }
}

set_exception_handler('exception_handler');

Class MyPDO extends PDO {

    public function __construct($debug = 0)
    {
        global $dsn, $dbuser, $dbpass;
        if ($dbuser != "") {
            parent::__construct($dsn, $dbuser, $dbpass);
        } else {
            parent::__construct($dsn);
        }
        if ($debug) {
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );  
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
        }
    }
}
?>
