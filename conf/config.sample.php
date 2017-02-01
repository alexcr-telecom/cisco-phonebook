<?php
    # Using php-pdo DSN
    # mysql
    $dsn = 'mysql:host=localhost;dbname=test';
    # postgresql
    #$dsn = 'pgsql:host=localhost;dbname=test";
    # sqlite3
    #$dsn = 'sqlite3:host=localhost;dbname=test";
    $dbuser = "test";
    $dbpass = "pwd";

    $email = "root@localhost";
    $debug = 2;
    $output_limit = 32;

    # query definitions
    $searchQry = "SELECT info as phonenumber, concat(firstname,lastname) as contact_name 
                    FROM contact
                    LEFT JOIN contactinfo ON contactinfo.contact_id=contact.id
                    WHERE contact_name LIKE ':searchname' 
                    ORDER BY name (:ordering) 
                    LIMIT(:offset, :max);";

    $companyQry = "SELECT concat(firstname,lastname) as contact_name 
                    FROM contact
                    ORDER BY name (:ordering) 
                    LIMIT(:offset, :max);";

    # defaults
    $default_action = "mainmenu";
    $default_searchname = "NONE";
?>
