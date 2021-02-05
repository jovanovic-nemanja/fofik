@extends('layouts.master')

@section('content')
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#modal-create-client').modal({backdrop: 'static', keyboard: false})
            $('#modal-create-client').modal('show');

            $('[data-toggle="tooltip"]').tooltip(); //Tooltip on icons top

            $('.popoverOption').each(function () {
                var $this = $(this);
                $this.popover({
                    trigger: 'hover',
                    placement: 'left',
                    container: $this,
                    html: true,

                });
            });

            let year = parseInt((new Date()).getFullYear());
            let month = parseInt((new Date()).getMonth()) + 1;
            
            $('select.year option[value=' + year + ']').attr('selected', true)
            $('select.month option[value=' + month + ']').attr('selected', true)

            drawChart();
            $('select.year').change(function () {
                year = $(this).val();
                drawChart();
            })
            $('select.month').change(function () {
                month = $(this).val();
                drawChart();
            })
            function initDailyVisionTrafficChart(data)
            {
                var googleData = data['google'];
                var amazonData = data['amazon'];
                var googleDataPoint = [];
                var amazonDataPoint = [];

                Object.keys(googleData).map(function (key, index) {
                    googleDataPoint.push({x: new Date(key), y: googleData[key]})
                })
                Object.keys(amazonData).map(function (key, index) {
                    amazonDataPoint.push({x: new Date(key), y: amazonData[key]})
                })
                var chart = new CanvasJS.Chart("daily-traffic", {
                    animationEnabled: true,
                    theme: "light2",
                    title:{
                        text: "Vision API Traffic(Daily)"
                    },
                    axisX:{
                        valueFormatString: "DD MMM",
                        crosshair: {
                            enabled: true,
                            snapToDataPoint: true
                        }
                    },
                    axisY: {
                        title: "Number of Searches",
                        crosshair: {
                            enabled: true
                        }
                    },
                    toolTip:{
                        shared:true
                    },  
                    legend:{
                        cursor:"pointer",
                        verticalAlign: "bottom",
                        horizontalAlign: "left",
                        dockInsidePlotArea: true,
                    },
                    data: [{
                        type: "line",
                        showInLegend: true,
                        name: "Google",
                        markerType: "square",
                        xValueFormatString: "DD MMM, YYYY",
                        color: "#F08080",
                        dataPoints: googleDataPoint
                    },
                    {
                        type: "line",
                        showInLegend: true,
                        name: "Amazon",
                        lineDashType: "dash",
                        dataPoints: amazonDataPoint
                    }]
                });
                chart.render();    
            }
            function initMonthlyVisionTrafficChart(data)
            {
                var googleData = data['google'];
                var amazonData = data['amazon'];
                var googleDataPoint = [];
                var amazonDataPoint = [];

                Object.keys(googleData).map(function (key, index) {
                    googleDataPoint.push({x: new Date(key), y: googleData[key]})
                })
                Object.keys(amazonData).map(function (key, index) {
                    amazonDataPoint.push({x: new Date(key), y: amazonData[key]})
                })
                var chart = new CanvasJS.Chart("monthly-traffic", {
                    animationEnabled: true,
                    theme: "light2",
                    title:{
                        text: "Vision API Traffic(Monthly)"
                    },
                    axisX:{
                        valueFormatString: "MMM YYYY",
                        crosshair: {
                            enabled: true,
                            snapToDataPoint: true
                        }
                    },
                    axisY: {
                        title: "Number of Searches",
                        crosshair: {
                            enabled: true
                        }
                    },
                    toolTip:{
                        shared:true
                    },  
                    legend:{
                        cursor:"pointer",
                        verticalAlign: "bottom",
                        horizontalAlign: "left",
                        dockInsidePlotArea: true,
                    },
                    data: [{
                        type: "line",
                        showInLegend: true,
                        name: "Google",
                        markerType: "square",
                        xValueFormatString: "MMMM, YYYY",
                        color: "#F08080",
                        dataPoints: googleDataPoint
                    },
                    {
                        type: "line",
                        showInLegend: true,
                        name: "Amazon",
                        lineDashType: "dash",
                        dataPoints: amazonDataPoint
                    }]
                });
                chart.render();    
            }
            function drawChart()
            {
                $.ajax({
                    url: '/vision-history',
                    type: 'get',
                    data: {
                        year: year,
                        month: month
                    },
                    success: function (result) {
                        if (result.success) {
                            initDailyVisionTrafficChart(result.data.daily)
                            initMonthlyVisionTrafficChart(result.data.monthly)
                        }
                    }
                })
            }
        });
    </script>
@endpush
        <!-- Small boxes (Stat box) -->
        
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-3 col-sm-3">
                <select class="form-control year mb">
                    <option value="2020">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                    <option value="2026">2026</option>
                </select>
            </div>
            <div class="col-lg-3 col-sm-3">
                <select class="form-control month mb">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-sm-6">
                <div id="monthly-traffic" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>  
            </div>
            <div class="col-lg-6 col-sm-6">
                <div id="daily-traffic" style="height: 370px; max-width: 920px; margin: 0px auto;"></div>  
            </div>
        </div>
@endsection
<style>
    .d-flex {
        display: flex;
    }
    .mb {
        margin-bottom: 10px;
    }
    .date {
        margin-top: 20px;
        margin-left: 20px;
    }
</style>
