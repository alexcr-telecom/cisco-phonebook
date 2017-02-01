<?php
require_once (dirname(__FILE__) . "/../conf/config.php");
require_once 'MDB2.php';

function db_connect()
{
    global $dsn, $debug;
    $options = array('debug' => $debug, 'result_buffering' => true);

    $DB = MDB2::factory($dsn, $options);
    if (PEAR::isError($DB)) {
    	$message="Can not connect to $dsn" . $DB->getMessage();
    	error_log("$message", 0);
        throw new Exception('Can not connect to database');
    }
    //$DB->setFetchMode(PEAR::DB_FETCHMODE_ASSOC);
    return $DB;
}
?>
