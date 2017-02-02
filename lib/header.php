<?php
  header("Content-type: text/xml");
  header("Connection: close"); 
  header("Expires: -1");
  print "<?xml version='1.0' encoding='utf-8'?>";
  $browser = @$_SERVER ['HTTP_USER_AGENT'] ?: "NONE";
  if ($browser != "NONE" && substr($browser,1,7) != "Allegro") {
      print "<?xml-stylesheet type='text/xsl' href='http://10.15.15.195/CiscoDirectory/lib/CiscoIPPhone.xslt'?>";
  }
?>
