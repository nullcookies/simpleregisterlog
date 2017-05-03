<h4><a href="/heatmap/<?php echo $id_map; ?>">Тепловая карта</a></h4>
<div class="graph-micro-wrapper">
	<div id="heatmap" class="graph map"></div>
</div>

<script>

$('#heatmap').svg({
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
