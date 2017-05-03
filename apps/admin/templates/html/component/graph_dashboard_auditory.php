<h4>Аудитория</h4>
<div class="graph-micro-wrapper">
	<div id="graph_auditory" class="graph"></div>
</div>

<script>
$(function () {
    $('#graph_auditory').highcharts({
        chart: {
            type: 'bar'
        },
        title: { text: null },
        xAxis: {
            categories: ['Возраст', 'Пол', 'OS'],
        },
        yAxis: {
            min: 0,
            title: { text: null },
        },
        plotOptions: {
            series: {
                stacking: 'percent',
                dataLabels: {
                    enabled: true,
                    /**format: '{series.name} {y}%',**/
                    formatter: function() {
                        if (this.y > 0) {
                            return this.series.name + ' ' + this.y + '%';
                        }
                    },
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                }
            },
        },
        legend: {
        	enabled: false
        },
        credits: {
            enabled: false
        },
        series: <?php echo json_encode($series); ?>,
    });
});
</script>
