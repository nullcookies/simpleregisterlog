<h4><?php echo $name ?></h4>
<div class="graph-micro-wrapper">
	<div id="graph_<?php echo $id; ?>" class="graph"></div>
</div>

<script>
$(function () {
    $('#graph_<?php echo $id; ?>').highcharts({
        chart: {
            type: 'bar'
        },
        title: { text: null },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.0f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        credits: {
            enabled: false
        },
        series: [{
            type: 'pie',
            name: 'Пользователей',
            dataLabels: {
                formatter: function () {
                    return this.y > 5 ? this.point.name : null;
                },
                color: 'white',
                distance: -50
            },
            data: <?php echo json_encode($data); ?>
        }]
    });
});
</script>
