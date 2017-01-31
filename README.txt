Here's a simple company directory for Cisco Voip phones

Here's what you need to do to get it working:

1) Make sure you have Apache and PHP up and running. Choose between either MySQL or LDAP server.
   
   MySQL: A new database has to be created first.
   
   LDAP: The searched name is looked at Objectclass person in Attributes: o,givenname,sn,cn,mail.
   Numbers are shown from Attributes in same class: telephonenumber,homePhone and mobile 

2) MySQL: Edit companyDirectory.php and change options in config section according to your needs.
   LDAP: Edit searchLDAP.php to change connection details of LDAP server, base CN is enough i.e. dc=intern,dc=net

3) MySQL: Edit searchDirectory.xml and change the URL to match
   the URL of companyDirectory on your Apache server.
   LDAP: Edit searchLDAP.xml

4) Edit directory.xml and change all URL occurances.

5) Edit the Cisco 7940/7960 .cnf file on your tftp server to point the directory to
   http://<your server>/directory.xml  For example:

   directory_url: "http://10.0.0.3/directory.xml"

   On newer 7941/7961 phones, the file is SEP<mac>.cnf.xml:
   
   <directoryURL>http://10.0.0.3/directory.xml</directoryURL>

6) MySQL: Edit DBConnect.php and enter settings of your MySQL in first line.

7) MySQL: Setup the MySQL database by running the following from the MySQL
   command line interface.

   CREATE TABLE phonebook (
     id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
     number VARCHAR(30),
     name VARCHAR(30),
     contact VARCHAR(30),
     mobile VARCHAR(30),
     homenumber VARCHAR(30),
     intern TINYINT,
     UNIQUE KEY(number)
   ); 

   (I'm assuming you've already set up a MySQL database and user, and
   are using that database when you create the table.)

8) MySQL: Populate the MySQL user table with data.  (I'll assume you know how to do
   this.  If not, learn MySQL so that you can use it!)

9) Reboot your phone, press the services key, and you should see a nice little
   menu.  Try it out! 
   The following script can be used in asterisk to reboot a cisco phone:
   
   /etc/asterisk/sip_notify.conf
   Event=>service-control
   Subscription-State=>active
   Content-Type=>text/plain
   Content=>action=reset
   Content=>RegisterCallId={${SIPPEER($PEERNAME},regcallid)}}
   Content=>ConfigVersionStamp={0000000000000000}
   Content=>DialplanVersionStamp={0000000000000000}
   Content=>SoftkeyVersionStamp={0000000000000000}

   In cli just type: sip notify cisco-reset {extension}
   
Disclaimer:  This code comes with no guarantee whatsoever.  It may or may not
work... it might even cause you problems.  It's worked for me...

Jared Smith
