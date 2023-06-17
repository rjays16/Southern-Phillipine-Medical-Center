<?php

# This is the database name
$dbname = 'hisdb';

# Database user name, default is root or httpd for mysql, or postgres for postgresql
$dbusername = 'hisdbuser';

# Database user password, default is empty char
$dbpassword = 's3gw0rxpr0ds3rv3r';

# Database host name, default = localhost
#$dbhost='192.168.1.41';
$dbhost = '10.1.80.73';
#$dbhost='10.1.80.50';


$ehr_host = '10.1.80.62/ehrprod';
$redis_host = '10.1.80.30';
$dbnamerep = 'hisdb';
$dbusernamerep = 'seniordev';
$dbpasswordrep = 's3n10r1t0d3v';
$dbhostrep = '10.1.80.97';
# First key used for simple chaining protection of scripts
$key = '3.53020914643E+013';

# Second key used for accessing modules
$key_2level = '826165905490';

# 3rd key for encrypting cookie information
$key_login = '1.13664924241E+013';

# Main host address or domain
$main_domain = '192.168.1.219';
#$main_domain='127.0.0.1';

# Host address for images
$fotoserver_ip = '192.168.1.219';
#$fotoserver_ip='127.0.0.1';

# Transfer protocol. Use https if this runs on SSL server
$httprotocol = 'http';

# Set this to your database type. For details refer to ADODB manual or goto http://php.weblogs.com/ADODB/
$dbtype = 'mysql';
$dbtypeuse = 'mysqlt';

# Set this to the FTP's user id.
$ftp_userid = 'segftpuser';

# Set this to the FTP users' password.
$ftp_passwrd = 's3gw0rx';


$config = array(
    'debug' => 0
);

define('NOTIFICATION_URI', 'ws://10.1.80.31:1234');
define('DIETARY_URL', 'http://10.1.80.27:8081/#');
define('DIETARY_API', 'https://10.1.80.27:8000');
define('DIETARY_PUBLIC_API', 'https://122.55.59.242:8000');

#define('java_dbaccess', "jdbc:mysql://$dbhost:3306/$dbname?user=$dbusername&password=$dbpassword");
#define('java_include', 'http://localhost:8080/JavaBridge/java/Java.inc');
#define('java_classpath', '/srv/tomcat/webapps/JavaBridge/WEB-INF/lib/');
#define('java_resource', '/srv/tomcat/webapps/JavaBridge/resource/');
#define('java_tmp', '/srv/tomcat/webapps/JavaBridge/tmp');
#define('java_cache', '/srv/tomcat/webapps/JavaBridge/cache/');

// $ehr_mobile_host = 'http://122.55.59.242:8076/api'; #okay nani
// $notification_socket  = 'http://122.55.59.242:6002';
// $notification_token  = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2MTg5MzM2MDAsImp0aSI6MSwiaXNzIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwNzJcL2Voci1iYWNrZW5kXC9wdWJsaWMiLCJuYmYiOjE2MTg5MzM2MDAsImV4cCI6MTYyMTUyNTYwMCwiZHRhIjp7InVzZXJfaWQiOiJib25peCJ9fQ.GTUthrYMie6D32aALhyrlwwg8AD33GdkKyJHDht1_uE'
// $notification_host  = 'http://122.55.59.242:8078/api';
/*Kutob diri*/

$telemed_host = '10.1.80.27:8078/api';
$telemed_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hMmQ1NTM4Mi5uZ3Jvay5pb1wvZWhyLWJhY2tlbmQtYXdzXC9wdWJsaWNcL2FwaVwvbG9naW4iLCJpYXQiOjE1NzMyMTU1ODYsImV4cCI6MTU3MzIxOTE4NiwibmJmIjoxNTczMjE1NTg2LCJqdGkiOiIzNmZlcmY3N0NoVmFtZDduIiwic3ViIjo1MDQsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.99DA-Qk7cW0eR369-d3cJm9apmzDVfupmRL_1IkTCFI';

$notification_host = '10.1.80.27:8078/api';
$notification_socket = 'http://10.1.80.27:6001';
$notification_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9hMmQ1NTM4Mi5uZ3Jvay5pb1wvZWhyLWJhY2tlbmQtYXdzXC9wdWJsaWNcL2FwaVwvbG9naW4iLCJpYXQiOjE1NzMyMTU1ODYsImV4cCI6MTU3MzIxOTE4NiwibmJmIjoxNTczMjE1NTg2LCJqdGkiOiIzNmZlcmY3N0NoVmFtZDduIiwic3ViIjo1MDQsInBydiI6Ijg3ZTBhZjFlZjlmZDE1ODEyZmRlYzk3MTUzYTE0ZTBiMDQ3NTQ2YWEifQ.99DA-Qk7cW0eR369-d3cJm9apmzDVfupmRL_1IkTCFI';

$ehr_mobile_host = 'http://10.1.80.27:8076/api';

define('onesignal', '168cd76f-9547-47ed-a4d4-fd893abcaddb');


//$dbname = 'hisdb';
//$dbusername = 'hisdbuser';
//$dbpassword = 's3gw0rxpr0ds3rv3r';
//$dbhost = '10.1.80.21';
////$dbname='hisdb';$dbusername='hisdbuser';$dbpassword='s3gw0rxpr0ds3rv3r';$dbhost='10.1.80.21';
//
//
//$ehr_host = 'ehr.local.com:8081';
//
//# First key used for simple chaining protection of scripts
//$key = '3.53020914643E+013';
//
//# Second key used for accessing modules
//$key_2level = '826165905490';
//
//# 3rd key for encrypting cookie information
//$key_login = '1.13664924241E+013';
//
//# Main host address or domain
//$main_domain = '127.0.0.1';
//
//# Host address for images
//$fotoserver_ip = '127.0.0.1';
//
//# Transfer protocol. Use https if this runs on SSL server
//$httprotocol = 'http';
//
//# Set this to your database type. For details refer to ADODB manual or goto http://php.weblogs.com/ADODB/
//$dbtype = 'mysql';
//$dbtypeuse = 'mysqlt';
//
//# Set this to the FTP's user id.
//$ftp_userid = 'segworks';
//
//# Set this to the FTP users' password.
//$ftp_passwrd = 's3gw0rx';
//
//#added by VAN 03-13-2012
//#transfer method to be used in connecting LIS
//#either NFS or SOCKET
//#$transfer_method = 'SOCKET';
//
//$debug_env = 1;
//define('DEBUG', 1);
//
//# new config variable
//$config = array(
//    'debug' => 1
//);
//
