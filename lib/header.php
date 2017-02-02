<?php
  header("Content-type: text/xml");
  print "<?xml version='1.0' encoding='utf-8'?>\n";

  $schema = "";
  $browser = @$_SERVER ['HTTP_USER_AGENT'] ?: "NONE";
  if ($render_html && $browser != "NONE" && substr($browser,1,7) != "Allegro") {
    header("Connection: close"); 
    header("Expires: -1");

    $path = "";
    if(isset($_SERVER['HTTP_HOST'])) {
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        $path = $protocol . "://" . $_SERVER['HTTP_HOST'];
    } else{
        $path = dirname(__DIR__);
    }
    $schema = " xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='$path/lib/CiscoIpPhone.xsd'";
    print "<?xml-stylesheet type='text/xsl' href='$path/lib/CiscoIPPhone.xslt'?>\n";
  }
?>
