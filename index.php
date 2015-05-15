<?php
	// this is configuring for our PHP server
	set_time_limit(0);
	ini_set('default_socket_timeout', 300);
	session_start();

	// we are using our defines and making our constants
	define('clientID',  '4578f1acb2e2424ebddc2d782e17f511');
	define('clientSecret',  'ab0f9f3550374f56a29ec4998465f41c');
	define('redirectURI',  'http://localhost/appacademyapi/index.php');
	define('ImageDirectory',  'pics/');
	// fucntion connecting to instagram
	function connectToInstagram($url) {
		$ch = curl_init();

		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2
		));

		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	// function to get userID because username doesn't allow us to get pictures
	function getUserID($userName){
		$url = 'https://api.instagram.com/v1/users/search?q='.$userName.'&client_id='.clientID;
		$instagramInfo = connectToInstagram($url);
		$results = json_decode($instagramInfo, true);
		// echoing userID
		return $results['data']['0']['id'];
	}
	// function to print out images onto screen 
	function printImages ($userID) {
		$url = 'https://api.instagram.com/v1/users/'.$userID.'/media/recent?client_id='.clientID.'&count=5';
		$instagramInfo = connectToInstagram($url);
		$results = json_decode($instagramInfo, true);
		// Parse through the info one by one
		foreach($results['data'] as $items){
			$image_url = $items['images']['low_resolution']['url']; //going through all the results and giving myself back the url of all the pictures because we want to save it in the php server
			echo '<img src =" '. $image_url .' "/><br/>';
			// calling a function to save $image_url
			savePictures($image_url);
		}
	}
	// functions to save image to server
	function savePictures($image_url) {
		echo $image_url.'<br>';
		$filename = basename($image_url);//filename is what we are storing, basename is the PHP built in method that we are using to store $image_url
		echo $filename . '<br>';

		$destination = ImageDirectory. $filename; //making sure the images doesnt ecist in the storage
		file_put_contents($destination, file_get_contents($image_url)); //gooes and grabs an imagefile and stores it into our server
	}
	// if statement checking for bullions true and not true
	// checking for get
	if (isset($_GET['code'])) {
		$code = ($_GET['code']);
		$url = 'https://api.instagram.com/oauth/access_token';
		// array is accessing the code
		$access_token_settings = array('client_id' => clientID,
									   'client_secret' => clientSecret,
									   'grant_type' => 'authorization_code',
									   'redirect_uri' => redirectURI,
									   'code' => $code
									   );
// cURL is what we use so that we can have interaction
// cURL is a way you hit url from your code to get an html response from it
		$curl = curl_init($url); //setting a curl sections.... url is where we are getting the date from
		curl_setopt($curl, CURLOPT_POST, true); //setting the options
		curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings); //setting the postfield to the array we created
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //setting it equal to 1 because we are getting strings back
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //set this to true in live work production

		$result = curl_exec($curl);
		curl_close($curl);

		$results = json_decode($result, true);
		
		$userName = $results['user']['username'];

		$userID = getUserID($userName);

		printImages($userID);
	}
	else {
?>
<!DOCTYPE html>
<html>
<head>
	<!-- basic html tags linking certain pages -->
	<meta charset="utf-8">
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Untitled</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="author" type="text/css" href="humans.txt">
</head>
<body>
	<!-- creating a login for people to go and give approval for our web to access their instagram account -->
	<!-- after we get the approval we are going to have the info so we can play with it -->
	<!-- echoing the constants and showing the code from instagram -->
	<a href="https://api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code">LOGIN</a>
	<script src-"js/main.js"></script>
</body>
</html>
<?php
	}
?>



