<?php
//require_once (dirname(__FILE__) . "/../conf/config.php");

function exception_handler($exception) 
{
    $outstr = 
"<CiscoIPPhoneMenu>
        <Title>Company Services</Title>
        <Prompt>Error Occurred: " . $exception->getMessage() . "</Prompt>
</CiscoIPPhoneMenu>\n";
    print($outstr);

    if (CONFIG_EMAIL) {
        error_log($exception->getMessage(), 1, CONFIG_EMAIL);
    } else {
        error_log($exception->getMessage(), 0);
    }
}

set_exception_handler('exception_handler');

Class MyPDO extends PDO {

    public function __construct()
    {
        if (!is_null(CONFIG_DBUSER)) {
            parent::__construct(CONFIG_DSN, CONFIG_DBUSER, CONFIG_DBPASS);
        } else {
            parent::__construct(CONFIG_DSN);
        }
        if (!is_null(CONFIG_DEBUG)) {
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );  
            $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );  
        }
    }
}
?>
