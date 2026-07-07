<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<title>Leaflet Test</title>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<style>

html,body{

height:100%;

margin:0;

}

#map{

height:100%;

}

</style>

</head>

<body>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>

var map=L.map('map').setView([20,0],2);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{

attribution:'© OpenStreetMap'

}).addTo(map);

</script>

</body>

</html>