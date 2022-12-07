@extends('layouts.backend')

@section('content')
<style>
.bootstrap-datetimepicker-widget.dropdown-menu{ width:auto; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('treatment.deleted_date_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.deleted_date_list') }}</li>
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
                <h3 class="card-title">{{ __('treatment.deleted_date_list') }}</h3><a class="btn btn-success btn-sm float-right" href="/listtreatmentdate">{{ __('treatment.active_dates') }}</a>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table text-nowrap table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('treatment.date') }}</th>
                    <th>{{ __('treatment.treatments') }}</th>
                    <th>{{ __('treatment.from') }}</th>
                    <th>{{ __('treatment.till') }}</th>
                    <th>{{ __('treatment.therapist') }}</th>
                    <th>{{ __('treatment.lunch') }}</th>
                    <th>{{ __('treatment.description') }}</th>
                    <th>{{ __('treatment.deleted_time') }}</th>
                    @can('Date Restore')
                    <th>{{ __('treatment.action') }}</th>
                    @endcan
                  </tr>
                  </thead>
                  <tbody>
                  @php $key = 0; @endphp
                  @foreach($dates as $value)
                  @php ++$key @endphp
                    <tr>
                        <td style="display:none;">
                        <form data-form="form-{{ $key }}">
                          @csrf
                          <input type="hidden" name="id" value="{{ $value->id }}">
                        </form>  
                          {{ $key }} 
                        </td>
                        <td style="min-width:200px;">
                          <div class="input-group">
                              <div class="input-group-append" data-target="#date-{{$key}}" data-toggle="datetimepicker">
                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                              </div>
                              <input data-form="{{ $key }}" type="text" name="date" class="form-control date" value="{{ \Carbon\Carbon::parse($value->date)->format($dateFormat->value) }}" id="date-{{$key}}">
                          </div>
                        </td>
                        <td  style="min-width:200px;">
                          <select data-form="{{ $key }}" name="treatment[]" class="form-control select2" multiple>  
                              @foreach($treatments as $tre)
                                @php $class = ''; @endphp
                                @foreach($value->treatments as $tr)
                                  @php if($tre->id == $tr->id) $class="selected";  @endphp
                                @endforeach
                                <option value="{{ $tre->id }}" {{ $class }} >{{ $tre->treatment_name }} ({{ $tre->inter }} min)</option>
                              @endforeach  
                            </select>
                                
                        </td>
                        <td>
                          <select data-form="{{ $key }}" name="from" class="form-control select2">
                            <x-time-range selected="{{ $value->from }}" :booked="$value->treatmentSlots" />
                          </select>
                        </td>
                        <td>
                          <select data-form="{{ $key }}" name="till" class="form-control select2">
                              <x-time-range selected="{{ $value->till }}" :booked="$value->treatmentSlots" />
                          </select>
                        </td>
                        <td>
                            <select data-form="{{ $key }}" name="therapist" class="form-control select2">
                              <option value="{{ $value->user_id }}" selected>{{ $value->user->name }} </option>
                            </select>
                        </td>
                        <td>
                          <select data-form="{{ $key }}" name="lunch" class="form-control select2">
                              <x-time-range selected="{{ $value->treatmentSlotLunch()->pluck('time') }}" :booked="$value->treatmentSlots" start="{{ $value->from }}" end="{{ $value->till }}" />
                          </select>
                        </td>
                        <td style="max-width:170px;">
                          <div class="input-group">
                              <input data-form="{{ $key }}" type="text" name="description" class="form-control" value="{{$value->description}}">
                          </div>
                        </td>
                        <td>
                          {{ \Carbon\Carbon::parse($value->updated_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s' )) }}
                        </td>
                        @can('Date Restore')
                        <td class='text-center'>
                            <a type="submit" class="btn btn-info btn-sm" style="margin:5px;" href="javascript:;" onclick="updateAjax({{$key}})">
                                <i class="nav-icon fas fa-edit"></i>
                                {{ __('treatment.restore') }}
                            </a>
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('treatment.date') }}</th>
                    <th>{{ __('treatment.treatments') }}</th>
                    <th>{{ __('treatment.from') }}</th>
                    <th>{{ __('treatment.till') }}</th>
                    <th>{{ __('treatment.therapist') }}</th>
                    <th>{{ __('treatment.lunch') }}</th>
                    <th>{{ __('treatment.description') }}</th>
                    <th>{{ __('treatment.deleted_time') }}</th>
                    @can('Date Restore')
                    <th>{{ __('treatment.action') }}</th>
                    @endcan
                  </tr>
                  </tfoot>
                </table>
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

jQuery(function () {
    //Initialize Select2 Elements
    jQuery('.select2').select2({
      theme: 'bootstrap4'
    });

});

@can('Date Restore')
function updateAjax(obj){

  event.preventDefault();
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  var form = jQuery("form[data-form='form-"+obj+"']");
  $.ajax({
      type: 'POST',
      url: '/restoreDateAjax',
      data: jQuery(form).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.dhbrs') }}'
          });
          jQuery(form).closest('tr').remove();
        }
        else if(data == 'exist'){
          Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.nesaa') }}'
          })
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.tiaueetrd') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.pcydatta') }}'
          })
      }
  });
}
@endcan


</script>
@stop