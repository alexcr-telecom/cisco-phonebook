<?php
    # Using php-pdo DSN
    # mysql
    $dsn = 'mysql:host=localhost;dbname=test';
    $dbuser = "test";
    $dbpass = "test";
    
    # postgresql
    #$dsn = "pgsql:host=localhost;dbname=asterisk";
    #$dbuser = "test";
    #$dbpass = "test";
    
    # sqlite3
    #$dsn = "sqlite:" . dirname(__FILE__) . "/sqlite3.db";
    $dsn = "sqlite:conf/sqlite3.db";

    $email = "root@localhost";
    $debug = 0;
    $output_limit = 32;

    # query definitions
    $searchQry = "SELECT info as phonenumber, firstname, lastname
                    FROM contact
                    LEFT JOIN contactinfo ON contactinfo.contact_id=contact.id
                    WHERE lastname LIKE :searchname
                    ORDER BY lastname ASC
                    LIMIT :offset, :max ;";

    $companyQry = "SELECT firstname, lastname
                    FROM contact
                    ORDER BY lastname ASC
                    LIMIT :offset, :max;";

    # defaults
    $default_action = "mainmenu";
    $default_searchname = "NONE";
?>
