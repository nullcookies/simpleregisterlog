<div class="map-control-panel">


	
	<div class="control-edit">
		<div class="label">Добавить<br>объекты</div>
		<button type="button" id="add-beacon"	class="btn btn-default btn-edit-mode"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="add-route"	class="btn btn-default btn-edit-mode"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="add-location"	class="btn btn-default btn-edit-mode"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="remove-route"	class="btn btn-default btn-edit-mode"><span class="glyphicon">&nbsp;</span></button>
	</div>

	<div class="control-show">
		<div class="label">Отобразить<br>слои</div>
		<!-- <button type="button" id="view-link-point"	class="btn btn-default"><span class="glyphicon">&nbsp;</span></button> -->
		<button type="button" id="view-beacon"		class="btn btn-default"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="view-route"		class="btn btn-default"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="view-location"	class="btn btn-default"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="get-route"		class="btn btn-default btn-edit-mode"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="view-heatmap"		class="btn btn-default"><span class="glyphicon">&nbsp;</span></button>
		<button type="button" id="view-link-point"	class="btn btn-default"><span class="glyphicon">&nbsp;</span></button>
	</div>	
</div>



<div class="map"></div>
<div class="info-panel"></div>

<style>

.route-line-one {
	stroke-width: 2;
	stroke: #ffa000;
	stroke-linecap: round;
}

.route-line-both {
	stroke-width: 2;
	stroke: green;
	stroke-linecap: round;
}

.route-location-link {
	stroke-width: 1;
	stroke: gray;
	stroke-linecap: round;
}

.route-new-line {
	stroke-width: 3;
	stroke: blue;
	stroke-linecap: round;
}

.calculated-route {
	fill: none;
	stroke-width: 3;
	stroke: red;
	stroke-linecap: round;
	stroke-linejoin: round;
}

.route-point-border {
	fill: none;
	stroke-width: 3;
	stroke: red;
}

.route-point-target {
	fill: none;
	stroke-width: 2;
	stroke: #ffa000;
}

.location-region-point {
	fill: #2D78FF;
	stroke: #005AFF;
	stroke-width: 2;
	opacity: 0.8;
}

.location-region-point:hover {
	stroke: #FF0000;
	stroke-width: 5;
}

.location-region-path {
	opacity: 0.3;
	stroke-width: 2;
}

.location-region-path:hover {
	opacity: 0.6;
	stroke-width: 3;
}

.location-region-new-path {
	opacity: 0.3;
	fill: #FF0000;
	stroke-width: 5;
	stroke: #FF0000;
}


</style>



<script>

$('.map').svg({
	onLoad: function(svg) {
		Map.svg = svg;
		Map.rasterWidth = <?php echo $rasterWidth; ?>;
		Map.rasterHeight = <?php echo $rasterHeight; ?>;
		Map.id_map = '<?php echo $id_map; ?>';
		Map.mapkey = '<?php echo $mapkey; ?>';
		
//		Map.view.beacons = true;
//		Map.view.routePoints = true;
		Map.view.locations = true;
//		Map.view.locationRegions = true;
		
		Map.init();
	}
});


$('#view-beacon').click(function () {
	var btn = $(this);
	if (btn.hasClass('active')) {
		btn.removeClass('active');
		Beacon.mapClear();
	} else {
		btn.addClass('active');
		Beacon.load();
	}
	btn.blur();
	Map.view.beacons = btn.hasClass('active');
});

$('#view-route').click(function () {
	var btn = $(this);
	if (btn.hasClass('active')) {
		btn.removeClass('active');
		RoutePoint.mapClear();
	} else {
		btn.addClass('active');
		RoutePoint.load();
	}
	btn.blur();
	Map.view.beacons = btn.hasClass('active');
});

$('#view-location').click(function () {
	var btn = $(this);
	if (btn.hasClass('active')) {
		btn.removeClass('active');
		Location.mapClear();
		LocationRegion.mapClear();
	} else {
		btn.addClass('active');
		LocationRegion.load();
	}
	btn.blur();
	Map.view.location = btn.hasClass('active');
});

$('#view-heatmap').click(function () {
	var btn = $(this);
	if (btn.hasClass('active')) {
		btn.removeClass('active');
		HeatMap.mapClear();
	} else {
		btn.addClass('active');
		HeatMap.load();
	}
	btn.blur();
	Map.view.heatMap = btn.hasClass('active');
});

$('#view-link-point').click(function () {
	var btn = $(this);
	if (btn.hasClass('active')) {
		btn.removeClass('active');
		LinkPoint.mapClear();
	} else {
		btn.addClass('active');
		LinkPoint.load();
	}
	btn.blur();
	Map.view.linkPoints = btn.hasClass('active');
});

$('.btn-edit-mode').click(function () {
	var btn = $(this);
	Map.clearEditTools();
	if (btn.hasClass('active')) {
		btn.removeClass('active');
		Map.clickAction = null;
		Map.mouseMoveAction = null;
	} else {
		$('.btn-edit-mode').removeClass('active');
		btn.addClass('active');
		Map.clickAction = btn.attr('id');
		Map.mouseMoveAction = null;
	}
	btn.blur();
});

</script>
