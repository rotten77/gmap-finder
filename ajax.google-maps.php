<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$return = array();

$adresa = isset($_GET['q']) ? trim($_GET['q']) : null;

if(is_null($adresa)) {
	$return['error'] = "Nebyla zadána adresa";
} else {

$url = "http://maps.google.com/maps/api/geocode/json?address=".urlencode($adresa)."&sensor=false";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:cs-CZ,cs;q=0.8'));
		$json = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($json, true);

		if(isset($result['results'][0]['geometry']['location'])) {

			$return['lat'] = $result['results'][0]['geometry']['location']['lat'];
			$return['lng'] = $result['results'][0]['geometry']['location']['lng'];

			// $return['lat_lng'] = $return['lat'].",".$return['lng'];

		} else {
			$return['error'] = "Adresa nenalezena";
		}

}

echo json_encode($return);
exit;