<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

// convert
// source: http://stackoverflow.com/questions/2548943/gps-format-in-php
function DECtoDMS($dec) {
    $vars = explode(".",$dec);
    $deg = $vars[0];
    $tempma = "0.".$vars[1];

    $tempma = $tempma * 3600;
    $min = floor($tempma / 60);
    $sec = $tempma - ($min*60);

    return array("deg"=>$deg,"min"=>$min,"sec"=>round($sec, 4));
} 

$return = array();

if(isset($_GET['convert'])) {

	$convertArray = explode(",", $_GET['convert']);

	$lat_format = DECtoDMS($convertArray[0]);
	$lng_format = DECtoDMS($convertArray[1]);

	$return['lat_format'] = $lat_format['deg']."° ".$lat_format['min']."' ".$lat_format['sec'].'"';

	$return['lng_format'] = $lng_format['deg']."° ".$lng_format['min']."' ".$lng_format['sec'].'"';
	echo json_encode($return);
	exit;	
}

$adresa = isset($_GET['q']) ? trim($_GET['q']) : null;

if(is_null($adresa)) {
	$return['error'] = "Specify address";
} else {

		/**
		 * GPS
		 */
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

			$lat_format = DECtoDMS($return['lat']);
			$lng_format = DECtoDMS($return['lng']);

			$return['lat_format'] = $lat_format['deg']."° ".$lat_format['min']."' ".$lat_format['sec'].'"';

			$return['lng_format'] = $lng_format['deg']."° ".$lng_format['min']."' ".$lng_format['sec'].'"';
			// $return['lng'] = $result['results'][0]['geometry']['location']['lng'];

			// $return['lat_lng'] = $return['lat'].",".$return['lng'];

				/**
				 * Address
				 */
				$url = "http://maps.google.com/maps/api/geocode/json?latlng=".urlencode($return['lat'].",".$return['lng'])."&sensor=false";

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, 0); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept-Language:cs-CZ,cs;q=0.8'));
				$json = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($json, true);

				if(isset($result['results'][0]['formatted_address'])) {
					$formatedArr = explode(",", $result['results'][0]['formatted_address']);
					if(count($formatedArr)>0) {
						foreach($formatedArr as $key=>$val) {
							$val = trim($val);

							if($val=="") unset($formatedArr[$key]);
							else $formatedArr[$key] = $val;
						}
					}
					$return['formatted_address'] = (count($formatedArr)>0 ? implode("\n", $formatedArr) : "");
					// $return['address_html'] = str_replace(",", "<br />", $result['results'][0]['formatted_address']);
				} else {
					$return['formatted_address'] = "";
				}


		} else {
			$return['error'] = "Address not found";
		}


}

echo json_encode($return);
exit;