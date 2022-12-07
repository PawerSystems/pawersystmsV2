@extends("layouts.backend")

@section('content')

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('keywords.dashboad') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('keywords.dashboad') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- <div class="row"> -->
          <div class="col-md-12">

            @can('Dashboard Graphs')
            <!-- LINE CHART -->
            <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">{{ __('reports.week_wise_booking_report') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>
                </div>
              </div>
              <div class="card-body">
                <div class="d-flex">
                  <p class="d-flex flex-column">
                    <span class="text-bold text-lg">
                    @php
                      $total = 0;
                    @endphp
                      @foreach($weekWiseDate as $data) 
                       @php
                       $total = $total + $data["booked"];
                       @endphp  
                      @endforeach
                      {{ $total }}
                    </span>
                    <span>{{ __('keywords.booking_over_time') }}</span>
                  </p>
                  
                </div>
                <!-- /.d-flex -->

                <div class="position-relative mb-4">
                  <canvas id="visitors-chart" height="200"></canvas>
                </div>

                <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> {{ __("reports.booked_slots") }}
                  </span>

                  <span>
                    <i class="fas fa-square text-gray"></i> {{ __("reports.free_slots") }}
                  </span>
                </div>
              </div>
            </div>
            
          </div>
          <div class="row">
            <div class="col-md-6">
              <!-- BAR CHART -->
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">{{ __('keywords.visitors_vs_bookings') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart">
                    <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  </div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>
            <div class="col-md-6">
              <!-- STACKED BAR CHART -->
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">{{ __('keywords.booked_slots_vs_free_slots') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div class="chart">
                    <canvas id="stackedBarChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                  </div>
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
            </div>

            <div class="col-md-6">
              <!-- Donut chart -->
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="far fa-chart-bar"></i>
                    {{ __('keywords.treatment_part_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span>
                  </h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div id="donut-chart" style="height: 700px;"></div>
                </div>
                <!-- /.card-body-->
              </div>
            </div>

            <div class="col-md-6">
              <!-- Donut chart -->
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="far fa-chart-bar"></i>
                    {{ __('keywords.age_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span>
                  </h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div id="age-chart" style="height: 700px;"></div>
                </div>
                <!-- /.card-body-->
              </div>
            </div>

            <div class="col-md-6">
              <!-- Donut chart -->
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="far fa-chart-bar"></i>
                    {{ __('keywords.gender_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span>
                  </h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <div id="gender-chart" style="height: 700px;"></div>
                </div>
                <!-- /.card-body-->
              </div>
            </div>

            @endcan
          </div>
          <!-- /.col (RIGHT) -->
        <!-- </div> -->
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@stop

@section('scripts')


<!-- page script -->
<script>
$(function () {
    /* ChartJS
     * -------
     * Here we will create a few charts using ChartJS
     */

    //--------------
    //- AREA CHART -
    //--------------
    var areaChartDataNew = {
    labels  : @json(array_keys($visitorsVsBookings)),
    //   labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [
        {
          label               : "{{ __('reports.bookings') }}",
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : @json(array_values(array_map(function($j) { if (array_key_exists("bookings",$j)) return $j['bookings'];}, $visitorsVsBookings))) 
          //-----Booked

        },
        {
          label               : "{{ __('reports.visitors') }}",
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : @json(array_values(array_map(function($j) { if (array_key_exists("visitors",$j)) return $j['visitors'];}, $visitorsVsBookings))) //----Not Booked
        },
      ]
    }
    // Get context with jQuery - using jQuery's .get() method.
    // var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

    var areaChartData = {
    labels  : @json($datesOnly),
    //   labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [
        {
          label               : '{{ __("reports.booked_slots") }}',
          backgroundColor     : 'rgba(60,141,188,0.9)',
          borderColor         : 'rgba(60,141,188,0.8)',
          pointRadius          : false,
          pointColor          : '#3b8bba',
          pointStrokeColor    : 'rgba(60,141,188,1)',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(60,141,188,1)',
          data                : @json($bookedArray) //-----Booked
        },
        {
          label               : '{{ __("reports.free_slots") }}',
          backgroundColor     : 'rgba(210, 214, 222, 1)',
          borderColor         : 'rgba(210, 214, 222, 1)',
          pointRadius         : false,
          pointColor          : 'rgba(210, 214, 222, 1)',
          pointStrokeColor    : '#c1c7d1',
          pointHighlightFill  : '#fff',
          pointHighlightStroke: 'rgba(220,220,220,1)',
          data                : @json($freeArray) //----Not Booked
        },
      ]
    }

    var areaChartOptions = {
      maintainAspectRatio : false,
      responsive : true,
      legend: {
        display: false
      },
      scales: {
        xAxes: [{
          gridLines : {
            display : false,
          }
        }],
        yAxes: [{
          gridLines : {
            display : false,
          }
        }]
      }
    }

    // This will get the first returned node in the jQuery collection.
    // var areaChart       = new Chart(areaChartCanvas, { 
    //   type: 'line',
    //   data: areaChartData, 
    //   options: areaChartOptions
    // })


    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = jQuery.extend(true, {}, areaChartDataNew)
    var temp0 = areaChartDataNew.datasets[0]
    var temp1 = areaChartDataNew.datasets[1]
    barChartData.datasets[0] = temp1
    barChartData.datasets[1] = temp0

    var barChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      datasetFill             : false
    }

    var barChart = new Chart(barChartCanvas, {
      type: 'bar', 
      data: barChartData,
      options: barChartOptions
    })

    //---------------------
    //- STACKED BAR CHART -
    //---------------------
    var stackedBarChartCanvas = $('#stackedBarChart').get(0).getContext('2d')
    var barChartData = jQuery.extend(true, {}, areaChartData)
    var stackedBarChartData = jQuery.extend(true, {}, barChartData)

    var stackedBarChartOptions = {
      responsive              : true,
      maintainAspectRatio     : false,
      scales: {
        xAxes: [{
          stacked: true,
        }],
        yAxes: [{
          stacked: true
        }]
      }
    }

    var stackedBarChart = new Chart(stackedBarChartCanvas, {
      type: 'bar', 
      data: stackedBarChartData,
      options: stackedBarChartOptions
    })


  //-------- Week wise report ----------
    var ticksStyle = {
      fontColor: '#495057',
      fontStyle: 'bold'
    }

    var mode      = 'index'
    var intersect = true
    var $visitorsChart = $('#visitors-chart')
    var visitorsChart  = new Chart($visitorsChart, {
    data   : {

      // labels  : ['18th', '20th', '22nd', '24th', '26th', '28th', '30th'],
      labels  : @json(array_keys($weekWiseDate)),

      
      datasets: [{
        type                : 'line',
        data                : [ @foreach($weekWiseDate as $data) '{{ $data["booked"] }}',  @endforeach ],
        backgroundColor     : 'transparent',
        borderColor         : '#007bff',
        pointBorderColor    : '#007bff',
        pointBackgroundColor: '#007bff',
        fill                : false
        // pointHoverBackgroundColor: '#007bff',
        // pointHoverBorderColor    : '#007bff'
      },
        {
          type                : 'line',
          data                : [ @foreach($weekWiseDate as $data) '{{ $data["free"] }}',  @endforeach ],
          backgroundColor     : 'tansparent',
          borderColor         : '#ced4da',
          pointBorderColor    : '#ced4da',
          pointBackgroundColor: '#ced4da',
          fill                : false
          // pointHoverBackgroundColor: '#ced4da',
          // pointHoverBorderColor    : '#ced4da'
        }]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          // ticks    : $.extend({
          //   beginAtZero : true,
          //   suggestedMax: {{ $total }}
          // }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  });


  /*
    * DONUT CHART
    * -----------
    */
  
  @php
    $currentYear = date('Y');
    $lessEqual20 = 0;
    $lessEqual29 = 0;
    $lessEqual39 = 0;
    $lessEqual49 = 0;
    $lessEqual59 = 0;
    $older = 0;
    foreach($treatmentsAgeWise as $value){
      if($value->user != NULL)
      {
        $age = $currentYear - ( $value->user->birth_year ?: '1975' );

        if( $age < 20 ){
          $lessEqual20++;
        }
        else if( $age >= 20 && $age <= 29 ){
          $lessEqual29++; 
        }
        else if( $age >= 30 && $age <= 39 ){
          $lessEqual39++; 
        }
        else if( $age >= 40 && $age <= 49 ){
          $lessEqual49++; 
        }
        else if( $age >= 50 && $age <= 59 ){
          $lessEqual59++; 
        }
        else if( $age > 59){
          $older++;    
        }
      }
    }
  @endphp

  var ageData = [
    {
      label: '<20',
      data : {{$lessEqual20}},
    },
    {
      label: '21-29',
      data : {{ $lessEqual29 }},
    },
    {
      label: '30-39',
      data : {{ $lessEqual39 }},
    },
    {
      label: '40-49',
      data : {{ $lessEqual49 }},
    },
    {
      label: '50-59',
      data : {{ $lessEqual59 }},
    },
    {
      label: '60-60>',
      data : {{ $older }},
    },
  ];

  var genderData = [
    @foreach($treatmentsGenderWise as $value)
    {
      label: '{{ __("profile.".$value->gender) }}',
      data : {{$value->ids}},
    },
    @endforeach
  ];

  var donutData = [
      @foreach($treatments as $treatment)
        {
          label: '{{ $treatment->title }}',
          data : {{ $treatment->ids }},
        },
      @endforeach
  ];

  
  $.plot('#donut-chart', donutData, {
    series: {
      pie: {
        show       : true,
        radius     : 1,
        label      : {
          show     : true,
          radius   : 2 / 3,
          formatter: labelFormatter,
          //threshold: 0.1
        }

      }
    },
    legend: {
      show: false
    }
  });

  $.plot('#age-chart', ageData, {
    series: {
      pie: {
        show       : true,
        radius     : 1,
        label      : {
          show     : true,
          radius   : 2 / 3,
          formatter: labelFormatter,
          //threshold: 0.1
        },
      }
    },
    legend: {
      show: false
    }
  });

  $.plot('#gender-chart', genderData, {
    series: {
      pie: {
        show       : true,
        radius     : 1,
        label      : {
          show     : true,
          radius   : 2 / 3,
          formatter: labelFormatter,
          //threshold: 0.1
        }

      }
    },
    legend: {
      show: false
    }
  });
  /*
    * END DONUT CHART
    */
      /*
  * Custom Label formatter
  * ----------------------
  */

});

</script>

@stop