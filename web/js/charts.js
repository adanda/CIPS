function printCheckstyleChart($container, $data)
{
    $.jqplot($container, $data, {
        title: 'Checkstyle Errors',
        axesDefaults: {
            labelRenderer: $.jqplot.CanvasAxisLabelRenderer
        },
        axes: {
            yaxis: {
                min: 0,
                pad: 1.1,
                tickInterval: 20
            },
            xaxis: {
                pad: 1.1
            }
        },
        series: [{color:'red'}]
    });
}