<?php
require_once (dirname(__FILE__) . "/../conf/config.php");

function exception_handler($exception) 
{
    global $debug, $email;
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Error Occurred: " . $exception->getMessage() . "</Prompt>
</CiscoIPPhoneMenu>\n";
    print($outstr);

    if ($debug) {
        error_log($exception->getMessage(), 1, $email);
    } else {
        error_log($exception->getMessage(), 0);
    }
    die('Uncaught exception: ' . $exception->getMessage());
}

set_exception_handler('exception_handler');

Class MyPDO extends PDO {

    public function __construct()
    {
        global $dsn, $dbuser, $dbpass;
        if ($dbuser != "") {
            parent::__construct($dsn, $dbuser, $dbpass);
        } else {
            parent::__construct($dsn);
        }
    }
}
?>
