<?php
  $dbhost = "localhost";
  $dbuser = "db_username";
  $dbpass = "db_password";
  $dbname = "asterisk";
  $email = "root@localhost";
  $dbtable = "phonebook";
  $debug = 0;
  $limit = 32;
  
  # query definitions
  $searchQry = "SELECT number as phonenumber, name as name WHERE name='?' ORDER BY name ? LIMIT(?, ?);";
  $companyQry = "SELECT number as phonenumber, name as name ORDER BY name ? LIMIT(?, ?);";
?>
