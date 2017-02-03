<?php
require_once (dirname(__FILE__) . "/header.php");
require_once (dirname(__FILE__) . "/mypdo.php");

function get_baseurl(){
    if(isset($_SERVER['HTTP_HOST'])) {
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    } else{
        return dirname(__DIR__) . "/phonebook.php";
    }
}
?>
