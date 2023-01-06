@extends('layouts.backend')

@section('content')
<style>
.bootstrap-datetimepicker-widget.dropdown-menu{ width:auto; }
.empty_trs{ display:none; }
.select2-container--bootstrap4 .select2-selection--multiple .select2-selection__choice{ float:none !important;}
.select2-container, td { max-width: fit-content; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('treatment.date_list') }} </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.date_list') }}</li>
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
                <h3 class="card-title"> {{ __('treatment.date_list') }}</h3>
                @can('Deleted Dates View')
                <a class="btn btn-primary btn-sm float-right" style="margin:5px;"  href="/treatmentDatesDeletedList">{{ __('treatment.deleted_dates') }}</a>
                @endcan
                <a class="btn btn-success btn-sm float-right" style="margin:5px;"  href="/pastdatelist">{{ __('treatment.past_dates') }}</a>
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
                    <th>{{ __('treatment.waiting_list') }}</th>
                    <th>{{ __('profile.location') }}</th>
                    <th>{{ __('treatment.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @php $key = 0; @endphp
                  @php $trs = ($dates->count() < 5 ? 10 : 0); @endphp
                  @foreach($dates as $value)
                    @php ++$key @endphp
                    <tr>
                        <td style="display:none;">
                        <form data-form="form-{{ $key }}">
                          @csrf
                          <input type="hidden" name="id" value="{{ $value->id }}">
                        </form>  
                        </td>
                        <td style="min-width:180px;">
                          <span>{{ \Carbon\Carbon::parse($value->date)->format('l') }}</span>
                          <div class="input-group">
                              <div class="input-group-append" data-target="#date-{{$key}}" data-toggle="datetimepicker">
                                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                              </div>
                              <input data-form="{{ $key }}" type="text" name="date" class="form-control date" value="{{ \Carbon\Carbon::parse($value->date)->format($dateFormat->value) }}" id="date-{{$key}}" readonly="readonly">
                              <input data-form="{{ $key }}" type="hidden" name="_date" value="{{$value->date}}" id="_date-{{$key}}">
                          </div>
                        </td>
                        <td  style="min-width:200px;">
                          <select data-form="{{ $key }}" name="treatment[]" class="form-control select2" multiple>  
                              @foreach($treatments as $tre)
                                @php $class = ''; @endphp
                                @foreach($value->treatments as $tr)
                                  @php if($tre->id == $tr->id) $class="selected";  @endphp
                                @endforeach
                                <option value="{{ $tre->id }}" {{ $class }} >{{ $tre->treatment_name }} ({{ $tre->time_shown ?: $tre->inter }} min)</option>
                              @endforeach  
                            </select>
                                
                        </td>
                        <td style="min-width:100px;">
                          <select data-form="{{ $key }}" name="from" class="form-control select2">
                            <x-time-range selected="{{ $value->from }}" :booked="$value->treatmentSlots" />
                          </select>
                        </td>
                        <td style="min-width:100px;">
                          <select data-form="{{ $key }}" name="till" class="form-control select2">
                              <x-time-range selected="{{ $value->till }}" :booked="$value->treatmentSlots" />
                          </select>
                        </td>
                        <td style="min-width:150px;">
                            <select data-form="{{ $key }}" name="therapist" class="form-control select2">
                              <option value="{{ $value->user_id }}" selected>{{ $value->user->name }} </option>
                              @foreach($therapists as $therapist)
                                <option value="{{ $therapist->id }}">{{ $therapist->name }}</option>
                              @endforeach    
                            </select>
                        </td>
                        <td style="min-width:100px;">
                          <select data-form="{{ $key }}" name="lunch" class="form-control select2">
                              <option class="badge d-none" value="none">{{ __('keywords.none') }}</option>
                              <x-time-range selected="{{ $value->treatmentSlotLunch()->pluck('time') }}" :booked="$value->treatmentSlots" start="{{ $value->from }}" end="{{ $value->till }}" />
                          </select>
                        </td>
                        <td style="min-width:100px;">
                          <input data-form="{{ $key }}" type="checkbox" name="waiting_list" id="waiting_list" class="form-control" {{$value->waiting_list ? 'checked' : ''}}>
                        </td>
                        <td style="max-width:170px;">
                          <div class="input-group">
                              <input data-form="{{ $key }}" type="text" name="description" class="form-control" value="{{$value->description}}">
                          </div>
                        </td>
                        <td class='text-center'>
                          @can('Date Edit')
                            <a type="submit" class="btn btn-info btn-sm" style="margin:5px;" href="javascript:;" onclick="updateAjax({{$key}})">
                                <i class="nav-icon fas fa-edit"></i>
                                {{ __('keywords.update') }}
                            </a>
                          @endcan
                          @can('Date Delete')  
                            <a class="btn btn-danger btn-sm" data-id="{{ md5($value->id) }}" style="margin:5px;" href="javascript:;" onclick="deleteDate(this)">
                                <i class="nav-icon fas fa-trash"></i>
                                {{ __('keywords.delete') }}
                            </a>
                          @endcan  
                        </td>
                    </tr>
                  @endforeach
                  @for($i = 1; $i < $trs; $i++)
                    <tr style="visibility: hidden;" class="empty_trs">
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th></th>
                    </tr>
                  @endfor
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('treatment.date') }}</th>
                    <th>{{ __('treatment.treatments') }}</th>
                    <th>{{ __('treatment.from') }}</th>
                    <th>{{ __('treatment.till') }}</th>
                    <th>{{ __('treatment.therapist') }}</th>
                    <th>{{ __('treatment.lunch') }}</th>
                    <th>{{ __('profile.location') }}</th>
                    <th>{{ __('treatment.action') }}</th>
                  </tr>
                  </tfoot>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

             <!-- Navigation dive starts here -->
             <div class="col-md-12">
              <div class="card">
                  <div class="card-header">
                      <nav aria-label="Contacts Page Navigation">
                          <ul class="pagination justify-content-center m-0">
                          {{ $dates->links() }} 
                          </ul>
                      </nav>
                  </div>
              </div> 
            </div>
            <!-- Navigation dive ends here -->
            
            </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@can('Date Delete')
<div class="modal" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('treatment.delete_date') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>{{ __('treatment.aysywtdtd') }}</p>
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="is_delete" id="is_delete" class="custom-control-input">
          <label class="custom-control-label" for="is_delete">{{ __('treatment.delete_permenantly') }}</label>
          <p style="color:red;"><i>({{ __('treatment.delete_note') }})</i></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success submit" data-dismiss="modal" onclick="DeleteConfirm(this)">{{ __('treatment.yes') }}</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('treatment.no') }}</button>
      </div>
    </div>
  </div>
</div>
@endcan
@stop

@section('scripts')
<script>

jQuery("div.input-group").bind("DOMSubtreeModified", function() {
  if(jQuery('.bootstrap-datetimepicker-widget').length == 0 ){
    jQuery('.empty_trs').hide();
  }
});

jQuery('div[data-toggle="datetimepicker"]').on('click',function(){
  if(jQuery('.bootstrap-datetimepicker-widget').length > 0 ){
    jQuery('.empty_trs').hide();
  }
  else{
    jQuery('.empty_trs').show();
  }
});


@can('Date Delete')
function DeleteConfirm(obj){

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  var isDelete = jQuery('#is_delete').is(":checked");
  var id = jQuery(obj).attr('data-id');
  var token = jQuery('meta[name="csrf-token"]').attr('content');

  $.ajax({
      type: 'POST',
      url: '/deleteDateAjax',
      data: { 'id':id,'isDelete':isDelete,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data.status == 'success'){
          Toast.fire({
              icon: 'success',
              title: '{{ __('treatment.dhbds') }}'
          });
          
          //------- Remove this tr from table ------//
          jQuery('a').each(function(){
            if(jQuery(this).attr('data-id') == id){
              jQuery(this).closest('tr').remove();
            }
          });

        }
        else if(data.status == 'exist'){
          var elems = jQuery(JSON.parse(data.ids));
          count = elems.length;
          
            jQuery(elems).each(function(key,value){
              $.ajax({
                type: 'POST',
                url: '/DeleteBooking',
                data: { 'id':value,'isDelete':isDelete,'_token':token },
                success: function (data) {
                  console.log(data);
                }
              });  
              if (!--count){
                DeleteConfirm(obj);
                Toast.fire({
                  icon: 'success',
                  title: '{{ __('treatment.dhbds') }}'
                });
                //------- Remove this tr from table ------//
                jQuery('a').each(function(){
                  if(jQuery(this).attr('data-id') == id){
                    jQuery(this).closest('tr').remove();
                  }
                });
              }
            });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.tsauetdd') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.pcydatta') }}'
          })
      }
  });
}
@endcan

@can('Date Delete')
function deleteDate(obj){
  jQuery('#deleteModal .submit').attr('data-id',jQuery(obj).attr('data-id'));
  $('#deleteModal').modal('show');
}
@endcan


@can('Date Edit')
function updateAjax(obj){

  event.preventDefault();
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 5000
  });

  var form = jQuery("form[data-form='form-"+obj+"']");
  var data = jQuery("input[data-form="+obj+"],select[data-form="+obj+"]");
  var that = data;
  $.ajax({
      type: 'POST',
      url: '/updateDateAjax',
      data: jQuery(form).serialize()+"&"+jQuery(data).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          //----- Update lunch slot o booked ---
          var txt = jQuery(that[5]).find(':selected').val();
          jQuery(that[5]).siblings('span').find('span[title="'+txt+'"]').html(txt+' (Booked)');
          jQuery(that[5]).find(':selected').html(txt+' (Booked)');

          Toast.fire({
              icon: 'success',
              title: '{{ __('treatment.dhbus') }}'
          })
        }
        else if(data == 'exist'){
          Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.nesaa') }}'
          })
        }
        else if(data == 'limit'){
          Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.ycstdbtasbit') }}'
          })
        }
        else{
          Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.pcydatta') }}'
          })
      }
  });
}
@endcan


</script>
@stop