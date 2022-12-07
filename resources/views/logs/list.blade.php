@extends('layouts.backend')
<style>
    svg.w-5.h-5{ max-width:30px; }
    .bottom-navigation nav > div:first-child {
        display: none;
    }
    .bottom-navigation{ padding: 20px; }
    .bottom-navigation span[aria-current="page"] > span{
        background-color: silver !important;
    }
</style>
@section('content')
@php $dateFormat = $timeFormat = ''; @endphp
@foreach($settings as $setting)  

    @if($setting->key == 'date_format')
        @php $dateFormat = $setting->value; @endphp 
        
    @elseif($setting->key == 'time_format')
        @php $timeFormat = $setting->value; @endphp 

    @endif

@endforeach
@php $month_ini = new DateTime("first day of this month"); @endphp
@php $month_end = new DateTime("last day of this month"); @endphp   
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('keywords.logs_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('keywords.logs_list') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
   
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">

          <div class="card">
              <div class="card-header">
                <div class="row">
                  <div class="col-6">
                      <h3 class="card-title">{{ __('keywords.logs_list') }}</h3>
                  </div>
                  <div class="col-6">
                      <form action="{{ Route('logSearch',session('business_name')) }}" method="POST" role="search">
                          {{ csrf_field() }}
                          <div class="input-group">
                              <input type="text" class="form-control" name="value"> <span class="input-group-btn">
                                  <button type="submit" class="btn btn-default" style="height: 38px;">
                                      <i class="fas fa-search"></i>
                                  </button>
                              </span>
                          </div>
                      </form>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6 offset-md-6">
                    <form id="logReportForm" action="{{ Route('getReport',session('business_name')) }}" method="POST">
                      @csrf
                      <input type="hidden" name="report_for" value="logReportForm">
                      <input type="hidden" name="type" value="ExcelReport">
                      
                      <div class="col-md-4 float-left">
                        <div class="form-group">
                          <div class="input-group">
                              <div class="input-group-append" data-target="#from" data-toggle="datetimepicker">
                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                              </div>
                              <input type="text" name="from" class="form-control float-right date" placeholder="{{ __('treatment.from') }}" value="{{ $month_ini->format($dateFormat) }}" id="from" readonly="readonly">
                              <input type="hidden" name="_from" id="_from" value="{{ $month_ini->format('Y-m-d') }}">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4 float-left">
                        <div class="form-group">
                          <div class="input-group">
                              <div class="input-group-append" data-target="#to" data-toggle="datetimepicker">
                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                              </div>
                              <input type="text" name="till" placeholder="{{ __('treatment.till') }}" class="form-control float-right date" id="to" readonly="readonly" value="{{ $month_end->format($dateFormat) }}">
                              <input type="hidden" name="_to" id="_to" value="{{ $month_end->format('Y-m-d') }}">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4 float-left">
                        <button type="submit" class="btn btn-warning btn-block">{{ __('keywords.downlaod') }}</button>
                      </div>
                    </form>
                  </div>
                </div>  
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('keywords.type') }}</th>
                    <th>{{ __('keywords.content') }}</th>
                    <th>{{ __('keywords.action_by') }}</th>
                    <th>{{ __('keywords.create_time') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach( $logs as $log )
                        <tr>
                            <td>{{ ucfirst(str_replace('_',' ',$log->model)) }}</td>
                            <td>{!! $log->comment !!}</td>
                            <td>{{ $log->action_by ?: 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->created_at)->format($dateFormat.' H:i:s') }}</td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
                <div class="bottom-navigation">
                    
                    {!! $logs->render() !!}
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->  


</div>               
@stop

@section('scripts')

<script>
function getReport(obj){

    event.preventDefault();
    console.log(jQuery(obj).serialize());
    return false;

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    $.ajax({
      type: 'POST',
      url: '/getReport',
      data: jQuery(obj).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('reports.dhbus') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('reports.tiauetus') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('reports.pcydatta') }}'
          })
      }
    });

}

</script>

@stop