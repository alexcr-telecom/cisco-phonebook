<?php
    # Using php-pdo DSN
    # mysql
    #$dsn = 'mysql:host=localhost;dbname=test';
    #$dbuser = "asterisk";
    #$dbpass = "asterisk";
    
    # postgresql
    #$dsn = "pgsql:host=localhost;dbname=asterisk";
    #$dbuser = "asterisk";
    #$dbpass = "asterisk";
    
    # sqlite3
    #$dsn = "sqlite:" . dirname(__FILE__) . "/sqlite3.db";
    $dsn = "sqlite:conf/sqlite3.db";

    $email = "root@localhost";
    $debug = 1;
    $output_limit = 32;
    #$render_html = 1;

    # query definitions
    $searchQry = "SELECT info as phonenumber, firstname, lastname
                    FROM contact
                    LEFT JOIN contactinfo ON contactinfo.contact_id=contact.id
                    WHERE {{searchBy}} LIKE :searchname
                    ORDER BY :orderBy ASC
                    LIMIT :offset, :max";

    $companyQry = "SELECT id, firstname, lastname
                    FROM contact
                    WHERE UPPER(SUBSTR({{searchBy}}, 1, 1)) BETWEEN :firstletter AND :lastletter
                    ORDER BY :orderBy ASC
                    LIMIT :offset, :max";

    # defaults
    $default_action = "MainMenu";
    $default_searchname = "NONE";
    $default_searchby = "lastname";
    $default_orderby = "lastname";
    $default_block = "AG";
    $default_order = "ASC";

    #testing
    /*
    $default_action = "search";
    $default_searchname = "g";
    $default_searchby = "lastname";
    $default_orderby = "lastname";
    */

    /*
    $default_action = "company";
    $default_searchname = "";
    $default_searchby = "lastname";
    $default_orderby = "lastname";
    */
?>
