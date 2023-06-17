<?php 
header('Content-type: application/x-java-jnlp-file'); 
echo "<?xml version='1.0' encoding='UTF-8' standalone='no' ?>";

require_once('./roots.php');
require_once($root_path.'include/inc_init_main.php');
require_once($root_path.'include/inc_environment_global.php');

$uri = $_SERVER['REQUEST_URI'];
$i = strripos($uri,"/");
$uri = substr($uri, 0, $i);
$url = $_SERVER['SERVER_ADDR'].$uri;
$socketAddr = BIOMETRIC_SOCKET_SERVER;
?>

<jnlp codebase="http://<?php echo $url; ?>" spec="1.0+">
    <information>
        <title>searchPatientUsingFingerprint</title>
        <vendor>Segworks Technologies Corporation</vendor>
        <homepage href=""/>
        <description>searchPatientUsingFingerprint</description>
        <description kind="short">searchPatientUsingFingerprint</description>
    </information>
    <update check="always" policy="always"/>
		<security>
		<all-permissions/>
	</security>
    <resources>
        <j2se version="1.8+"/>
        <jar href="SearchPatientUsingFingerprint.jar" main="true"/>
		<jar href="lib/dpfpenrollment.jar"/>
		<jar href="lib/dpfpverification.jar"/>
		<jar href="lib/dpotapi.jar"/>
		<jar href="lib/dpotjni.jar"/>
		<jar href="lib/mysql-connector-java-5.1.23-bin.jar"/>
		<jar href="lib/engine.io-client-0.6.0.jar"/>
		<jar href="lib/json-20090211.jar"/>
		<jar href="lib/okhttp-2.4.0.jar"/>
		<jar href="lib/okhttp-ws-2.4.0.jar"/>
		<jar href="lib/okio-1.4.0.jar"/>
		<jar href="lib/socket.io-client-0.6.0.jar"/>
		<jar href="lib/jcalendar-1.4.jar"/>
		<jar href="lib/commons-pool2-2.4.2.jar"/>
		<jar href="lib/jedis-3.4.1.jar"/>
		<property name="jnlp.secure.argument.*" value="true" />
		<property name="jnlp.dbhost" value="<?php echo $dbhost.":3306"; ?>"/>
		<property name="jnlp.dbname" value="<?php echo $dbname; ?>"/>
		<property name="jnlp.dbusername" value="<?php echo $dbusername; ?>"/>
		<property name="jnlp.dbpassword" value="<?php echo $dbpassword; ?>"/>               
		<property name="jnlp.socketServer" value="<?php echo $socketAddr; ?>"/>
		<property name="jnlp.redishost" value="<?php echo $redis_host; ?>"/>		
	</resources>
    <application-desc main-class="searchpatientusingfingerprint.MainForm">
        <argument><?php echo $_GET['clientId']; ?></argument>                                             
    </application-desc>
</jnlp>