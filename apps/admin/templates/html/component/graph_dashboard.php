<h4><?php echo $title; ?></h4>
<div class="graph-micro-wrapper">
	<div id="graph_<?php echo $id ?>" class="graph"></div>
</div>

<script>
$(function () {
    $('#graph_<?php echo $id ?>').highcharts({
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
