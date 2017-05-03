<?php echo $title; ?>
<?php if ($filters) : ?>
<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-body">
			<?php echo $filters->render('filters') ?>
		</div>	
	</div>
</div>
<?php endif; ?>

<div class="graph-wrapper">
	<div id="graph"></div>
</div>

<script>
$(function () {
    $('#graph').highcharts({
        chart: {
            type: '<?php echo $graphType; ?>'
        },
        title: { text: null },
        xAxis: {
            categories: <?php echo json_encode($names); ?>,
            title: { text: null }
        },
        yAxis: {
            min: 0,
            title: { text: null },
            labels: {
                overflow: 'justify'
            }
        },
        plotOptions: {
            bar: {
                dataLabels: {
                    enabled: true
                }
            },
        },
        <?php if (count($series) < 2) : ?>
        legend: {
        	enabled: false
        },
        <?php endif; ?>
        credits: {
            enabled: false
        },
        series: <?php echo json_encode($series); ?>,
    });
});
</script>
