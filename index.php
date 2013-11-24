<?php
$q = isset($_GET['q']) ? $_GET['q'] : "";
$out = isset($_GET['out']) ? $_GET['out'] : "html";

if($out=="json") {
	include "./ajax.google-maps.php";
}

?><!DOCTYPE html>
<html lang="cs">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Google Maps GPS Finder</title>
		<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet" />
		<link href="./style.css" rel="stylesheet" />
		<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
<?php
	// Google Analytics tracking on my site
	if(preg_match("/rotten77/", $_SERVER['SERVER_NAME'])) include dirname(__FILE__) . "/../ga.html";
?>
	</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-md-4">
			<form class="form-inline" id="frm-q">
				<div class="form-group">
					<input type="text" class="form-control input-lg" size="20" name="q" id="q" value="<?php echo $q; ?>" placeholder="Address&hellip;" />	
				</div>
				<button type="submit" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-search"></i></button>
			</form>

			<div id="result">
					<div class="panel panel-default">
						<div class="panel-body">
						<form class="form-vertical">
							<h4>DEC</h4>
							<div class="form-group col-lg-6">
								<label for="gsp_lat">Lat</label>
								<input type="text" id="gps_lat" name="gps_lat" class="form-control" />
							</div>

							<div class="form-group col-lg-6">
								<label for="gps_lng">Lng</label>
								<input type="text" id="gps_lng" name="gps_lng" class="form-control" />
							</div>
							
							<div class="clearfix"></div>

							<div class="form-group col-lg-12">
								<label for="gps_ll">Lat+Lng</label>
								<input type="text" id="gps_ll" name="gps_ll" class="form-control" />
							</div>
							<hr />
							<h4>DMS</h4>
							<div class="form-group col-lg-6">
								<label for="gsp_lat_format">Lat</label>
								<input type="text" id="gps_lat_format" name="gps_lat_format" class="form-control" />
							</div>

							<div class="form-group col-lg-6">
								<label for="gps_lng_format">Lng</label>
								<input type="text" id="gps_lng_format" name="gps_lng_format" class="form-control" />
							</div>
							<div class="clearfix"></div>

							<div class="form-group col-lg-12">
								<label for="gps_ll_format">Lat+Lng</label>
								<input type="text" id="gps_ll_format" name="gps_ll_format" class="form-control" />
							</div>					
						</form>
						</div>

					</div>
			</div>
		</div>
		
		<div class="col-md-8">
			<div id="map">
				<noscript>
					<p>Enable JS</p>
				</noscript>
			</div>
		</div>
	</div>

</div>



<script src="./bootstrap/js/jquery.js"></script>
<script src="./bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script> 
<script src="./jquery.ui.map.min.js"></script>
<script>
$(function(){

	$('#map').gmap({'zoom': 6, 'disableDefaultUI':false, 'center': '49.93000812460691, 15.435791015625'}).bind('init', function(evt, map) {



			$(map).click(function(event) {
					$('#map').gmap('clear', 'markers');
					$('#map').gmap('addMarker', {
						'position': event.latLng, 
						'draggable': false, 
						'bounds': false
						}, function(map, marker) {
							$('#gps_lat').val(marker.position.lat());
							$('#gps_lng').val(marker.position.lng());
							$('#gps_ll').val($('#gps_lat').val()+","+$('#gps_lng').val());

							$.getJSON('./ajax.google-maps.php?convert='+marker.position.lat()+","+marker.position.lng(), function(data) {
								$('#gps_lat_format').val(data['lat_format']);
								$('#gps_lng_format').val(data['lng_format']);
								$('#gps_ll_format').val($('#gps_lat_format').val()+","+$('#gps_lng_format').val());							
							});


					}); 
					$('#result').show();
			});
			// najít adresu
			$('#frm-q').submit(function(){
				$('#result').hide();
				var adresa = $('#q').val();

				$.getJSON('./ajax.google-maps.php?q='+adresa, function(data) {
					if(data['error']) {
						alert(data['error']);
					} else {
						$('#result').show();
						$('#gps_lat').val(data['lat']);
						$('#gps_lng').val(data['lng']);
						$('#gps_ll').val($('#gps_lat').val()+","+$('#gps_lng').val());

							$('#gps_lat_format').val(data['lat_format']);
							$('#gps_lng_format').val(data['lng_format']);
							$('#gps_ll_format').val($('#gps_lat_format').val()+","+$('#gps_lng_format').val());
						

						var gpslatlng = new google.maps.LatLng($('#gps_lat').val(), $('#gps_lng').val());

						$('#map').gmap('clear', 'markers');
						$('#map').gmap('addMarker', {
							'position': gpslatlng, 
							'draggable': false, 
							'bounds': false
							}); 
						map.setCenter(gpslatlng);
						map.setZoom(15);

					}
				});
				return false;
			});

			if($('#q').val()!="") $('#frm-q').submit();

	});

	$('input[type="text"]').click(function(){
		$(this).select();
	});


});
</script>
</body>
</html>

