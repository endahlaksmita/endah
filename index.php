<?php
	if(isset($_POST["submit"])) {
		$nama = $_POST["nama_cabang"];
		$latitude = $_POST["latitude"];
		$longitude = $_POST["longitude"];

		// proses tabah data
	}
?>

<html lang="en">
	<head>
		<title>Pom Bensin</title>

		<link href="assets/css/bootstrap.css" rel="stylesheet" />
		<link href="assets/css/bootstrap-responsive.css" rel="stylesheet" />

		<style type="text/css">
			body { padding-top:60px; }

			.controls {
		        margin-top: 10px;
		        border: 1px solid transparent;
		        border-radius: 2px 0 0 2px;
		        box-sizing: border-box;
		        -moz-box-sizing: border-box;
		        height: 32px;
		        outline: none;
		        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
		      }

		      #pac-input {
		        background-color: #fff;
		        font-family: Roboto;
		        font-size: 15px;
		        font-weight: 300;
		        margin-left: 12px;
		        padding: 0 11px 0 13px;
		        text-overflow: ellipsis;
		        width: 300px;
		      }

		      #pac-input:focus {
		        border-color: #4d90fe;
		      }

		      .pac-container {
		        font-family: Roboto;
		      }

		      #type-selector {
		        color: #fff;
		        background-color: #4d90fe;
		        padding: 5px 11px 0px 11px;
		      }

		      #type-selector label {
		        font-family: Roboto;
		        font-size: 13px;
		        font-weight: 300;
		      }

		</style>
	</head>

	<body>
		<div class="container">

		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="container">
					<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</a>

					<a class="brand" href="web2/index.php">SPBU Malang</a>

					<div class="btn-group pull-right"></div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="span8">
				<div class="control-group">
					<input id="pac-input" class="controls" type="text" placeholder="Enter a location" />

					<div id="type-selector" class="controls">
						<input type="radio" name="type" id="changetype-all" checked="checked" />
						<label for="changetype-all">Semua</label>

						<input type="radio" name="type" id="changetype-establishment" />
						<label for="changetype-establishment">Bangunan</label>

						<input type="radio" name="type" id="changetype-address" />
						<label for="changetype-address">Alamat</label>

						<input type="radio" name="type" id="changetype-geocode" />
						<label for="changetype-geocode">Geografi</label>
					</div>


					<div id="map_canvas" style="width:100%; height:500px"></div>
				</div>
			</div>

			<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST"> 
			<div class="span4">
				<div class="control-group">
					<label class="control-label" for="input01">Nama Cabang</label>

					<input type="text" class="input-xlarge" id="nama_cabang" name="nama_cabang" rel="popover" data-content="Masukkan nama cabang." data-original-title="Cabang" />
				</div>

				<div class="control-group">
					<label class="control-label" for="input01">Longitude</label>

					<input type="text" class="input-xlarge" id="longitude" name="longitude" />
				</div>

				<div class="control-group">
					<label class="control-label" for="input01">Latitude</label>

					<input type="text" class="input-xlarge" id="latitude" name="latitude" />
				</div>

				<div class="control-group">
					<label class="control-label" for="input01"></label>

					<button type="submit" name="submit" class="btn btn-success">Tambah Cabang</button>
				</div>
			</form>

			<div class="control-group">
				<label class="control-label" for="input01">Daftar Cabang</label>

				<div id="daftar">
					<ul>
						<?php
							require('config.php');
							// mengambil data dari database
							$lokasi = mysql_query("select * from `cabang`");

							while($l = mysql_fetch_array($lokasi)) {
								// membuat fungsi javascript untuk nantinya diolah dan ditampilkan dalam peta
								
								echo "<li><a href=\"javascript:setpeta(".$l['lat'].",".$l['long'].",".$l['id'].")\">".$l['nama_cabang']."</a> | <a href='?action=remove&id=".$l['id']."'>Hapus</a></li>";
							}
						?>
					</ul>
				</div>
			</div>
		</div>

		<hr />

		<footer>
			<p>&copy; API GOOGLE MAP</p>
		</footer>

		<script src="http://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
		<script src="assets/js/bootstrap-alert.js"></script>
		<!-- <script src="http://maps.google.com/maps/api/js?sensor=false"></script> -->

		<script type="text/javascript">
			function initMap() {
				var map = new google.maps.Map(document.getElementById('map_canvas'), {
					center: {lat: -7.966238599999999, lng: 112.60980080000002},
					zoom: 15
				});

				var input = (document.getElementById('pac-input'));
				var types = document.getElementById('type-selector');

				map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
				map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

				var autocomplete = new google.maps.places.Autocomplete(input);
				autocomplete.bindTo('bounds', map);

				var infowindow = new google.maps.InfoWindow();

				var marker = new google.maps.Marker({
					map: map,
					anchorPoint: new google.maps.Point(0, -29)
				});

				autocomplete.addListener('place_changed', function() {
					infowindow.close();
					marker.setVisible(false);

					var place = autocomplete.getPlace();

					if(!place.geometry) {
						window.alert("No details available for input: '" + place.name + "'");
						return;
					}

					if(place.geometry.viewport) {
						map.fitBounds(place.geometry.viewport);
					} else {
						map.setCenter(place.geometry.location);
						map.setZoom(17);
					}

					marker.setIcon(({
						url: place.icon,
						size: new google.maps.Size(71, 71),
						origin: new google.maps.Point(0, 0),
						anchor: new google.maps.Point(17, 34),
						scaledSize: new google.maps.Size(35, 35)
					}));

					marker.setPosition(place.geometry.location);
					marker.setVisible(true);

					var address = '';

					if(place.address_components) {
						address = [
							(place.address_components[0] && place.address_components[0].short_name || ''),
							(place.address_components[1] && place.address_components[1].short_name || ''),
							(place.address_components[2] && place.address_components[2].short_name || '')
						].join(' ');
					}

					infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
					infowindow.open(map, marker);
				});

				function setupClickListener(id, types) {
					var radioButton = document.getElementById(id);

					radioButton.addEventListener('click', function() {
						autocomplete.setTypes(types);
					});
				}

				setupClickListener('changetype-all', []);
				setupClickListener('changetype-address', ['address']);
				setupClickListener('changetype-establishment', ['establishment']);
				setupClickListener('changetype-geocode', ['geocode']);
			}


			// var peta;
			// var gambar_tanda;
			// gambar_tanda = 'assets/img/marker.png';

			// function peta_awal() {
			// 	// posisi default peta saat diload
			// 	var lokasibaru = new google.maps.LatLng(-7.966238599999999,112.60980080000002);
			// 	var petaoption = {
			// 		zoom: 13,
			// 		center: lokasibaru,
			// 		mapTypeId: google.maps.MapTypeId.ROADMAP
			// 	};
				
			// 	peta = new google.maps.Map(document.getElementById("map_canvas"),petaoption);
				
			// 	// ngasih fungsi marker buat generate koordinat latitude & longitude
			// 	tanda = new google.maps.Marker({
			// 		position: lokasibaru,
			// 		map: peta, 
			// 		icon: gambar_tanda,
			// 		draggable : true
			// 	});
				
			// 	// ketika markernya didrag, koordinatnya langsung di selipin di textfield
			// 	google.maps.event.addListener(tanda, 'dragend', function(event){
			// 			document.getElementById('latitude').value = this.getPosition().lat();
			// 			document.getElementById('longitude').value = this.getPosition().lng();
			// 	});
			// }

			// function setpeta(x,y,id) {
			// 	// mengambil koordinat dari database
			// 	var lokasibaru = new google.maps.LatLng(x, y);
			// 	var petaoption = {
			// 		zoom: 14,
			// 		center: lokasibaru,
			// 		mapTypeId: google.maps.MapTypeId.ROADMAP
			// 	};
				
			// 	peta = new google.maps.Map(document.getElementById("map_canvas"),petaoption);
				 
			// 	 // ngasih fungsi marker buat generate koordinat latitude & longitude
			// 	tanda = new google.maps.Marker({
			// 		position: lokasibaru,
			// 		icon: gambar_tanda,
			// 		draggable : true,
			// 		map: peta
			// 	});
				
			// 	// ketika markernya didrag, koordinatnya langsung di selipin di textfield
			// 	google.maps.event.addListener(tanda, 'dragend', function(event){
			// 			document.getElementById('latitude').value = this.getPosition().lat();
			// 			document.getElementById('longitude').value = this.getPosition().lng();
			// 	});
			// }
		</script>

		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA6PHLAT-IiFMFPhE2cAryVvS9WJILz-IA&libraries=places&callback=initMap"></script>
	</body>
</html>
