<?php 
header('Content-type: application/x-java-jnlp-file'); 
echo "<?xml version='1.0' encoding='UTF-8' standalone='no' ?>"
?>

<jnlp codebase="http://127.0.0.1:1111/searchPatientUsingFingerprint" spec="1.0+">
    <information>
        <title>searchPatientUsingFingerprint</title>
        <vendor>Len2x</vendor>
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
		<jar href="lib/jedis-2.8.1.jar"/>
	</resources>
    <application-desc main-class="searchpatientusingfingerprint.MainForm">
	<argument><?php echo $_GET['ptntId']; ?></argument>
    </application-desc>
</jnlp>