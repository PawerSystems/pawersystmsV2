@extends("layouts.backend")

@section('content')

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('stats.stats') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('stats.stats') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">

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

          <div class="col-md-12">

            <!-- STACKED BAR CHART -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">{{ __('stats.stacked_bar_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>

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

            <!-- AREA CHART -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">{{ __('stats.area_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            
            <!-- BAR CHART -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">{{ __('stats.bar_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>

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

            <!-- LINE CHART -->
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">{{ __('stats.line_chart') }} <span class='small'><i>{{'( '.__('keywords.duration_of').' '.$duration.' '.__('keywords.month').'(s) )' }}</i></span></h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->


          </div>
          <!-- /.col (RIGHT) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@stop

@section('scripts')

<!-- ChartJS -->
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>

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

    // Get context with jQuery - using jQuery's .get() method.
    var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

    var areaChartData = {
    labels  : @json($datesOnly),
    //   labels  : ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
      datasets: [
        {
          label               : '{{ __('stats.booked_slote') }}',
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
          label               : '{{ __('stats.free_slote') }}',
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
    var areaChart       = new Chart(areaChartCanvas, { 
      type: 'line',
      data: areaChartData, 
      options: areaChartOptions
    })

    //-------------
    //- LINE CHART -
    //--------------
    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
    var lineChartOptions = jQuery.extend(true, {}, areaChartOptions)
    var lineChartData = jQuery.extend(true, {}, areaChartData)
    lineChartData.datasets[0].fill = false;
    lineChartData.datasets[1].fill = false;
    lineChartOptions.datasetFill = false

    var lineChart = new Chart(lineChartCanvas, { 
      type: 'line',
      data: lineChartData, 
      options: lineChartOptions
    })


    //-------------
    //- BAR CHART -
    //-------------
    var barChartCanvas = $('#barChart').get(0).getContext('2d')
    var barChartData = jQuery.extend(true, {}, areaChartData)
    var temp0 = areaChartData.datasets[0]
    var temp1 = areaChartData.datasets[1]
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
  });

  

  /*
    * DONUT CHART
    * -----------
    */

    //--- Neck / Shoulder
    //-- Upper Back
    //--- Lower Back

  @php
    $currentYear = date('Y');
    $lessEqual20 = 0;
    $lessEqual29 = 0;
    $lessEqual39 = 0;
    $lessEqual49 = 0;
    $lessEqual59 = 0;
    $older = 0;
    foreach($treatmentsAgeWise as $value){
      $age = $currentYear - $value->user->birth_year;

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
        }

      }
    },
    legend: {
      show: false
    }
  });

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
</script>

@stop