<?php 
header('Content-type: application/x-java-jnlp-file'); 
echo "<?xml version='1.0' encoding='UTF-8' standalone='no'?>";

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

$uri = $_SERVER['REQUEST_URI'];
$i = strripos($uri,"/");
$uri = substr($uri, 0, $i);
$url = $_SERVER['SERVER_ADDR'].$uri;
$socketAddr = BIOMETRIC_SOCKET_SERVER;
?>
<jnlp codebase="http://<?php echo $url; ?>" spec="1.0+">
    <information>
        <title>RegisterPatientFingerprint</title>
        <vendor>Segworks Technologies Corporation</vendor>
        <homepage href=""/>
        <description>RegisterPatientFingerprint</description>
        <description kind="short">RegisterPatientFingerprint</description>
    </information>
    <update check="always" policy="always"/>
    <security>
        <all-permissions/>
    </security>
    <resources>
        <j2se version="1.8+"/>
        <jar href="RegisterPatientFingerprint.jar" main="true"/>
        <jar href="lib/dpfpenrollment.jar"/>
        <jar href="lib/dpfpverification.jar"/>
        <jar href="lib/dpotapi.jar"/>
        <jar href="lib/dpotjni.jar"/>
        <jar href="lib/socket.io-client-0.6.0.jar"/>
        <jar href="lib/engine.io-client-0.6.0.jar"/>
        <jar href="lib/okhttp-2.4.0.jar"/>
        <jar href="lib/okio-1.4.0.jar"/>
        <jar href="lib/okhttp-ws-2.4.0.jar"/>
        <jar href="lib/gson-2.8.2.jar"/>
        <jar href="lib/json-20090211.jar"/>
        <property name="jnlp.secure.argument.*" value="true" />
		<property name="jnlp.socketServer" value="<?php echo $socketAddr; ?>"/>
    </resources>
    <application-desc main-class="registerpatientfingerprint.MainForm">
        <argument><?php echo isset($_GET['clientId']) ? $_GET['clientId'] : ''; ?></argument>
    </application-desc>
</jnlp>