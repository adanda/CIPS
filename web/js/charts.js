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
        series: [{color:'#e73131', label: 'Errors', showMarker: false, fill: false, fillAlpha: 0.3}],
        legend: { show:true, placement: 'outside', location: 's' }
    });
}

function printTestresultChart($container, $data)
{
    $.jqplot($container, $data, {
        title: 'Testresults',
        axesDefaults: {
            labelRenderer: $.jqplot.CanvasAxisLabelRenderer
        },
        axes: {
            yaxis: {
                min: 0,
                pad: 1.1,
                tickInterval: 10
            },
            xaxis: {
                pad: 1.1
            }
        },
        series: [
            {color: '#000000', label: 'Tests', showMarker: false, fill: false, fillAlpha:0.3},
            {color: '#62cd3d', label: 'Assertions', showMarker: false, fill: false, fillAlpha: 0.3},
            {color: '#e73131', label: 'Failures', showMarker: false, fill: false, fillAlpha: 0.3},
            {color: '#ed973b', label: 'Errors', showMarker: false, fill: false, fillAlpha: 0.3}
        ],
        legend: { show:true, placement: 'outside', location: 's' }
    });
}

function printCoverageChart($container, $data)
{
    $.jqplot($container, $data, {
        title: 'Coverage',
        axesDefaults: {
            labelRenderer: $.jqplot.CanvasAxisLabelRenderer
        },
        axes: {
            yaxis: {
                min: 0,
                pad: 1.1,
                tickInterval: 10
            },
            xaxis: {
                pad: 1.1
            }
        },
        series: [
            {color: '#62cd3d', label: 'Code Coverage', showMarker: false, fill: false, fillAlpha: 0.3},
        ],
        legend: { show:true, placement: 'outside', location: 's' }
    });
}