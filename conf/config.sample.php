<?php
    # Using php-pdo DSN
    # mysql
    $dsn = 'mysql:host=localhost;dbname=test';
    # postgresql
    #$dsn = "pgsql:host=localhost;dbname=asterisk";
    # sqlite3
    #$dsn = "sqlite:" . dirname(__FILE__) . "/sqlite3.db";
    
    $dbuser = "asterisk";
    $dbpass = "MxAsterisk_5";

    $email = "root@localhost";
    $debug = 0;
    $output_limit = 32;

    # query definitions
    $searchQry = "SELECT info as phonenumber, concat(firstname,lastname) as contact_name 
                    FROM contact
                    LEFT JOIN contactinfo ON contactinfo.contact_id=contact.id
                    WHERE contact_name LIKE :searchname
                    ORDER BY contact_name (:ordering) 
                    LIMIT(:offset, :max);";

    $companyQry = "SELECT concat(firstname,lastname) as contact_name 
                    FROM contact
                    ORDER BY contact_name (:ordering) 
                    LIMIT(:offset, :max);";

    # defaults
    $default_action = "mainmenu";
    $default_searchname = "NONE";
?>
