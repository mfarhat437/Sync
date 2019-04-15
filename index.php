
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<title>notifications</title>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
  var OneSignal = window.OneSignal || [];
  OneSignal.push(function() {
    OneSignal.init({
      appId: "f6a198a6-f4bf-4a2e-8453-6074278da008",
    });
  });
</script>    </head>
	<body>
    
    <h2> onesignal notification</h2>
    
    </body>
</html>
<script type="text/javascript">>
OneSignal.push(function() {
  /* These examples are all valid */
  var isPushSupported = OneSignal.isPushNotificationsSupported();
  if (isPushSupported) {
    // Push notifications are supported
    console.log('supported')
      OneSignal.isPushNotificationsEnabled(function(isEnabled) {
    if (isEnabled){
      console.log("Push notifications are enabled!");
   } else{
      console.log("Push notifications are not enabled yet.");   
        OneSignal.showHttpPrompt();


   }
  });

  } else {
    // Push notifications are not supported
        console.log(' not supported')

  }
});
    </script