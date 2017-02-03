<?php
    # Using php-pdo DSN
    # mysql
    #define('CONFIG_DSN', "mysql:host=localhost;dbname=test");
    #define('CONFIG_DBUSER', "asterisk");
    #define('CONFIG_DBPASSS', "asterisk");
    
    # postgresql
    #define('CONFIG_DSN', "pgsql:host=localhost;dbname=asterisk");
    #define('CONFIG_DBUSER', "asterisk");
    #define('CONFIG_DBPASSS', "asterisk");
    
    # sqlite3
    #$dsn = "sqlite:" . dirname(__FILE__) . "/sqlite3.db";
    define('CONFIG_DSN', 'sqlite:conf/sqlite3.db');
    define('CONFIG_DBUSER', NULL);
    define('CONFIG_DBPASSS', NULL);

    define('CONFIG_EMAIL', "root@localhost");
    define('CONFIG_DEBUG',  True);
    define('CONFIG_OUTPUT_LIMIT', 32);
    define('CONFIG_RENDERASHTML', False);

    # query definitions
    define('CONFIG_SEARCH_QUERY', "SELECT info as phonenumber, firstname, middlename, lastname
                    FROM contact
                    LEFT JOIN contactinfo ON contactinfo.contact_id=contact.id
                    WHERE {{searchBy}} LIKE :searchname
                    ORDER BY :orderBy ASC
                    LIMIT :offset, :max");

    define('CONFIG_COMPANY_QUERY', "SELECT id, firstname, middlename, lastname
                    FROM contact
                    WHERE UPPER(SUBSTR({{searchBy}}, 1, 1)) BETWEEN :firstletter AND :lastletter
                    ORDER BY :orderBy ASC
                    LIMIT :offset, :max");

    # defaults
//    /*    
    define('CONFIG_DEFAULT_ACTION', "MainMenu");
    define('CONFIG_DEFAULT_SEARCHNAME', NULL);
    define('CONFIG_DEFAULT_SEARCHBY', "lastname");
    define('CONFIG_DEFAULT_ORDERBY', "lastname");
    define('CONFIG_DEFAULT_BLOCK', "AG");
//    */

    #testing
    /*
    define('CONFIG_DEFAULT_ACTION', "search");
    define('CONFIG_DEFAULT_SEARCHNAME', "g");
    define('CONFIG_DEFAULT_SEARCHBY', "lastname");
    define('CONFIG_DEFAULT_ORDERBY', "lastname");
    define('CONFIG_DEFAULT_BLOCK', "AG");
    */

    /*
    define('CONFIG_DEFAULT_ACTION', "search");
    define('CONFIG_DEFAULT_SEARCHNAME', 1);
    define('CONFIG_DEFAULT_SEARCHBY', "id");
    define('CONFIG_DEFAULT_ORDERBY', "lastname");
    define('CONFIG_DEFAULT_BLOCK', "AG");
    */

    /*
    define('CONFIG_DEFAULT_ACTION', "company");
    define('CONFIG_DEFAULT_SEARCHNAME', NULL);
    define('CONFIG_DEFAULT_SEARCHBY', "lastname");
    define('CONFIG_DEFAULT_ORDERBY', "lastname");
    define('CONFIG_DEFAULT_BLOCK', "AG");
    */
?>
