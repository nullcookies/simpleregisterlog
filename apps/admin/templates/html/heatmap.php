<div class="container-fluid heatmap-control">
	<div class="panel panel-default">
		<div class="panel-body">
			<?php echo $filters->render('filters') ?>
		</div>	
	</div>
</div>

<div class="map"></div>

<script>

$('.map').svg({
	onLoad: function(svg) {
		Map.svg = svg;
		Map.rasterWidth = <?php echo $rasterWidth; ?>;
		Map.rasterHeight = <?php echo $rasterHeight; ?>;
		Map.id_map = '<?php echo $id_map; ?>';
		Map.mapkey = '<?php echo $mapkey; ?>';
		
		Map.view.heatMap = true;
		
		Map.init();
	}
});

</script>
