<?php
    # mysql
    $dsn = 'mysql://dbuser:dbpassword@localhost/dbname';
    # postgresql
    #$dsn = 'pgsql://dbuser:dbpassword@localhost/dbname';
    #$dsn = 'pgsql://dbuser:dbpassword@unix(/var/run/postgresql/.s.PGSQL.5432)/dbname';
    # sqlite3
    #$dsn = array('phptype'  => 'sqlite', 'database' => 'dbname', 'mode'     => '0644');

    # see: http://pear.php.net/manual/en/package.database.mdb2.intro-connect.php for more DSN examples

    $email = "root@localhost";
    $dbtable = "phonebook";
    $debug = 2;
    $output_limit = 32;

    # query definitions
    $searchQry = "SELECT number as phonenumber, name as name FROM $dbtable WHERE name='?' ORDER BY name (?) LIMIT(?, ?);";
    $companyQry = "SELECT number as phonenumber, name as name FROM $dbtable ORDER BY name (?) LIMIT(?, ?);";

    # defaults
    $default_action = "mainmenu";
    $default_searchname = "NONE";
?>
