@extends('layouts.backend')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('part.treatment_areas_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('part.treatment_areas_list') }}</li>
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
                <h3 class="card-title">{{ __('part.treatment_areas_list') }}</h3>
                @can('Treatment Area Create')
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modal-default">{{ __('part.add_new') }}</button>
                @endcan
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('part.title') }}</th>
                    @foreach ( Config::get('languages') as $key => $val )
                      @if ($key == 'en')
                        @continue
                      @else
                        <th>{{ $val['display'] }}</th>
                      @endif
                    @endforeach
                    <th>{{ __('part.order') }}</th>
                    <th>{{ __('part.status') }}</th>
                    <th>{{ __('part.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($parts as $key => $value)
                        <tr>
                            <td>
                                <p style="display:none;">{{ $value->title }}</p>
                                <input type="hidden" name="id" value="{{ $value->id }}" >
                                <input type="text" class="form-control" name="title" value="{{$value->title}}">
                            </td>

                            @foreach ( Config::get('languages') as $key => $val )
                              @if ($key == 'en')
                                @continue
                              @else
                                @php $Tname =  $key; $Tvalue =  ''; @endphp 
                                @foreach($value->translations as $translation)
                                  @if($translation->key == $key)
                                    @php $Tname =  $translation->key; $Tvalue =  $translation->value; @endphp
                                  @endif
                                @endforeach
                                <td>
                                  <input type="text" class="form-control" name="{{ $Tname }}" value="{{ $Tvalue }}">
                                </td>
                              @endif
                            @endforeach

                            <td>
                                <input min="0" type="number" class="form-control" name="order" value="{{ $value->torder }}">
                            </td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}" onclick="statusChange(this)" style="cursor: pointer;">
                                    {{ $value->is_active ? __('part.active') : __('part.deactive') }}
                                </span>
                            </td>
                            <td class='text-center'>
                              <div class="btn-group inline pull-left">
                                @can('Treatment Area Edit')
                                  <button class="btn btn-info btn-sm" onclick="updateMethod(this)">
                                      <i class="nav-icon fas fa-edit"></i>
                                      {{ __('keywords.update') }}
                                  </button>
                                @endcan
                                @can('Treatment Area Delete')  
                                  <button class="btn btn-danger btn-sm" onclick="deleteMethod(this)">
                                      <i class="nav-icon fas fa-trash"></i>
                                      {{ __('keywords.delete') }}
                                  </button>
                                @endcan  
                              </div>
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
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

@can('Treatment Area Create')
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('part.create_area') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ Route('addPart',session('business_name')) }}" method="post" id="saveMethod">
            @csrf
            <div class="input-group mb-3">
              <input type="hidden" name="order" value="1">
                <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="{{ __('part.title') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            @if ($errors->has('title'))
                <span class="text-danger">{{ $errors->first('title') }}</span>
            @endif
            </div>

            @foreach ( Config::get('languages') as $key => $val )
            @if ($key == 'en')
              @continue
            @endif
            <div class="input-group mb-3">
                <input type="text" name="{{ $key }}" value="" class="form-control" placeholder="{{ $val['display'] }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
              @if ($errors->has($key))
                <span class="text-danger">{{ $errors->first($key) }}</span>
              @endif
            </div>
          @endforeach


        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('part.close') }}</button>
          <button type="submit" class="btn btn-primary">{{ __('part.save') }}</button>
        </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
@endcan
@stop

@section('scripts')


<!-- Showing error or success messages -->
@if(Session::get('success'))
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});
        Toast.fire({
            icon: 'success',
            title: '{{ Session::get("success") }}'
      })
  }); 
</script> 
@elseif( Session::get('error') )
<script type="text/javascript">
  jQuery(function() {
		const Toast = Swal.mixin({
		  toast: true,
		  position: 'top-end',
		  showConfirmButton: false,
		  timer: 3000
		});
        Toast.fire({
            icon: 'error',
            title: '{{ Session::get("error") }}'
        })
  }); 
</script> 
@endif


<script type="text/javascript">
$(document).ready(function () {
  $.validator.setDefaults({
    submitHandler: function () {
        return true;
    }
  });
  $('#saveMethod').validate({
    rules: {
        title: {
            required: true
        }
    },
    messages: {
        title: {
        required: "{{ __('part.please_enter_area_title') }}"
      }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});
</script>

<script>
@can('Treatment Area Edit')
function updateMethod(obj){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    @foreach ( Config::get('languages') as $key => $val )
      @if ($key == 'en')
        @continue
      @else
        var {{$key}} = jQuery(obj).closest('tr').find('input[name={{$key}}]').val();
      @endif
    @endforeach  


    var title = jQuery(obj).closest('tr').find('input[name=title]').val();
    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var order = jQuery(obj).closest('tr').find('input[name=order]').val();
    var token = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
      type: 'POST',
      url: '/updatePartAjax',
      data: { 
        @foreach ( Config::get('languages') as $key => $val )
          @if ($key == 'en')
            @continue
          @endif  
           '{{$key}}':{{$key}},
        @endforeach  
        'id':id,'title':title,'order':order,'_token':token 
      },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('part.dhbus') }}'
          });
        }
        else if(data == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('part.treatment_part_already_exist') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('part.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('part.pcydatta') }}'
          })
      }
    });
}
@endcan
@can('Treatment Area Delete')
function deleteMethod(obj){
  const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      url: '/deletePartAjax',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('part.dhbds') }}'
          });
          jQuery(obj).closest('tr').remove();
        }
        else if(data == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('part.tpiiusycndi') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('part.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('part.pcydatta') }}'
          })
      }
    });
}
@endcan

@can('Treatment Area Edit')
function statusChange(obj){
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    var id = jQuery(obj).closest('tr').find('input[name=id]').val();
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
      type: 'POST',
      url: '/updatePartStatusAjax',
      data: { 'id':id,'_token':token },
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
          Toast.fire({
              icon: 'success',
              title: ' {{ __('part.dhbus') }}'
          });

          if( jQuery(obj).hasClass('bg-danger') ){
            jQuery(obj).removeClass('bg-danger').addClass('bg-success');
            jQuery(obj).html('Active');
          }
          else{
            jQuery(obj).removeClass('bg-success').addClass('bg-danger');
            jQuery(obj).html('Deactive');
          }
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('part.tiauetud') }}'
          })
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('part.pcydatta') }}'
          })
      }
    });

}
@endcan

</script>

@stop