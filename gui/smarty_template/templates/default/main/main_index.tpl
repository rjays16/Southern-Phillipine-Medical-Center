{{* main_index.tpl *}}
<html>
<head>
<title>SegHIS - Southern Philippines Medical Center Hospital Information System</title>
<link rel="stylesheet" href="images/template_css.css" type="text/css" />

</style>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><style type="text/css">body {
	margin-left: 0px;
	margin-right: 0px;
}
</style>
<link rel="stylesheet" href="./css/sticky/sticky.full.css" type="text/css" />
<script type="text/javascript" src="js/jsprototype/prototype.js"></script>
<script type='text/javascript' src="./js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript">
	$j = jQuery.noConflict();
	function resizeContent() {
		$('contentMenu').style.height=(window.innerHeight-$('banner').height-4)+'px';
		$('contentFrame').style.height=(window.innerHeight-$('banner').height-4)+'px';
	}
</script>
</head>

<body style="overflow:hidden " onLoad="resizeContent();" onResize="resizeContent();">

	<table align="center" border="0" cellpadding="0" cellspacing="0" height="*" width="100%">
		<tbody>
			<tr>
				<td height="*" valign="top" bgcolor="#FFFFFF">
					<a name="up" id="up"></a>
					<iframe src="login_lnk.php" id="banner" name="banner" width="100%" frameborder="0" scrolling="no" height="124">banner frame</iframe>

					<table style="border: 1px solid rgb(153, 160, 170);" border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
						<tbody>
							<tr>
								<td valign="top">
									<div id="contentMenu" style="width:150px;overflow-x:visible;overflow-y:hidden">
										<table align="center"  border="0" cellpadding="0" cellspacing="0" width="100%" style="height:100%;overflow-x:display;overflow-y:scroll;">
											<tbody>
												<tr>
													<td width="20%" height="100%" valign="top" style="border-right: 0px solid rgb(153, 160, 170); border-bottom: 1px solid rgb(255, 255, 255);">
														<table width="150" height="100%" border="0" cellpadding="0" cellspacing="0">
															<tbody>
																<tr>
																	<td valign="top" height="100%" style="">
																		<table width="100%" border="0" height="100%" cellpadding="0" cellspacing="0">
																			<tbody>
																				<tr>
																					<td valign="top" style="height:100%;">
																						{{$sMainMenu}}
																					</td>
																				</tr>
																			</tbody>
																		</table>
																	</td>
																</tr>
															</tbody>
														</table>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</td>
								<td width="100%" align="center" valign="top" bgcolor="#D2DEE3" style="border-left: 0px solid rgb(255, 255, 255); border-right: 1px solid rgb(255, 255, 255); border-bottom: 1px solid rgb(255, 255, 255);">
									<iframe id="contentFrame" src="{{$startPage|default:"main/startframe.php"}}" name="contframe" width="100%" height="700" frameborder="0" scrolling="auto">***</iframe>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" id="onesignalid" name="onesignalid">
	<script>
		localStorage.ehrMobileHost = "{{$ehr_mobile_host}}";
		localStorage.notifToken = "{{$notification_token}}";
		localStorage.notifSocketHost = "{{$notification_socket}}";
		localStorage.username = "{{$username}}";
	</script>
	<iframe id="notifcontIf" src="./socket.html" style="border-style:none;height: 0px;"></iframe>
</body>

<script type="text/javascript" src="./js/sticky/sticky.full.js"></script>
<script type="text/javascript" src="./js/notification/autobahn/autobahn.min.js"></script>
<script type="text/javascript" src="./js/notification/notification.HISRealtimeConnection.js"></script>
<script type="text/javascript" src="./js/notification/notification.main.js"></script>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
	var appid = "{{$appID}}";
	console.log("app id from init_main:", appid);
	var OneSignal = window.OneSignal || [];

	function getplayerid(){
		OneSignal.getUserId().then(function(userId) {
		   	console.log('OneSignal ID',userId);
		   	document.getElementById('onesignalid').value = userId;
   		});
	}

    OneSignal.push(function() {
       OneSignal.init({
            appId: appid,
        }).then(function() {
        	
		   OneSignal.provideUserConsent(true);
		   getplayerid();

		   OneSignal.on('notificationDisplay', function(event) {
			   console.error('OneSignal notification displayed:', event);
		   });

		   OneSignal.push(["addListenerForNotificationOpened", function(data) {
			   console.error("Received NotificationOpened:");
			   console.error(data);
		   }]);

		   var isPushSupported = OneSignal.isPushNotificationsSupported();
		   if (!isPushSupported) {
			   console.error('Push notification not supported.');
		   }

		   OneSignal.isPushNotificationsEnabled().then(function(isEnabled) {
			   if (!isEnabled)
				   console.error("Push notifications are not enabled yet.");
		   });

		   OneSignal.push(["getNotificationPermission", function(permission) {
			   console.log("Site Notification Permission:", permission);
			   // (Output) Site Notification Permission: default
			   if(permission != 'granted')
				   OneSignal.showNativePrompt();
		   }]);
        });
    });

    OneSignal.push(function(){
		OneSignal.on('subscriptionChange', function (isSubscribed) {
	    	console.log("The user's subscription state is now:", isSubscribed);
	    	if(isSubscribed){
	    		getplayerid();
	    	}
	  	});
    });
</script>
<!-- <script type="text/javascript" src="./js/notification/notification.area.js"></script> -->
</html>