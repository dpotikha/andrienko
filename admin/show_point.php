<?php
$_SERVER['DOCUMENT_ROOT'] = '/var/www/places.kiev.ua';
require_once($_SERVER['DOCUMENT_ROOT']."/admin/prepend.php");
//edump($_GET['c']);
?>
<html>
<body style="margin: 0;">
<script type="text/javascript" src="/js/jquery-min2.js"></script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=false&language=ru"></script>
<script type="text/javascript">
    function getPoint(p) {
        var point = p.split(',');
        return new google.maps.LatLng(point[0], point[1]);
    }

    function initialize() {
        var mapOptions = {
          zoom: 14,
          center: getPoint('<?=$_GET['c']?>'),
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
        var marker = new google.maps.Marker({
            position: getPoint('<?=$_GET['c']?>'),
        });
        marker.setMap(map);
    };

    $(document).ready(function() {
        initialize();
    } );
</script>
<div id="map_canvas" style="height:100%; wifth:100%"></div>
</body>
</head>
