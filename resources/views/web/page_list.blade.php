@extends('layouts.backend')

@section('content')
<div class="content-wrapper">
<br><br>
    <section class="content">
        <div class="container-fluid">        
        <div class="card">
                <div class="card-header">
                <h3 class="card-title">{{ __('web.web_pages') }}</h3>
                @can('Website Pages Edit')
                <button class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modal-default">{{ __('web.create_link') }}</button>
                <h3 class="card-title float-right"><a class="btn btn-info btn-sm" href="{{ route('pagesGenerate',session('business_name')) }}">{{ __('web.reset_pages') }}</a></h3>
                @endcan
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                <table class="table table-bordered">
                    <thead>                  
                    <tr>
                        <th>{{ __('web.page') }}</th>
                        <th style="width: 40px">{{ __('web.status') }}</th>
                        @can('Website Pages Edit')<th>{{ __('web.action') }}</th>@endcan
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pages as $key => $value)
                        <tr>
                            <td>{{ $value->title ?: $value->page }}</td>
                            <td><span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">{{ $value->is_active ? __('web.active') : __('web.deactive') }}</span></td>
                        @can('Website Pages Edit')
                            <td class='text-center'><a class="btn btn-info" href="{{ Route('editPage',array(session('business_name'),md5($value->id))) }}"><i class="nav-icon fas fa-edit"></i>
                            {{ __('keywords.edit') }}</a></td>
                        @endcan    
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                <!-- /.card-body -->
              
            </div>
            <!-- /.card -->
        </div>
    </section>    
</div> 

@can('Website Pages Edit')
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('web.add_new_link') }}</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ Route('addLink',session('business_name')) }}" method="post" id="saveMethod">
            @csrf

            <label>English</label>

            <div class="input-group mb-3">
                <input type="text" name="titleEN" value="{{ old('titleEN') }}" class="form-control" placeholder="{{ __('web.link_title') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            @if ($errors->has('titleEN'))
                <span class="text-danger">{{ $errors->first('titleEN') }}</span>
            @endif
            </div>

            <div class="input-group mb-3">
                <input type="text" name="urlEN" value="{{ old('urlEN') }}" class="form-control" placeholder="{{ __('web.link_url') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-globe-europe"></i>
                    </div>
                </div>
            @if ($errors->has('urlEN'))
                <span class="text-danger">{{ $errors->first('urlEN') }}</span>
            @endif
            
            </div>

            <label>Danish</label>

            <div class="input-group mb-3">
              <input type="text" name="titleDK" value="{{ old('titleDK') }}" class="form-control" placeholder="{{ __('web.link_title') }}">
              <div class="input-group-append">
                  <div class="input-group-text">
                      <i class="fas fa-shield-alt"></i>
                  </div>
                </div>
            @if ($errors->has('titleDK'))
                <span class="text-danger">{{ $errors->first('titleDK') }}</span>
            @endif
            </div>
            
            <div class="input-group mb-3">
                <input type="text" name="urlDK" value="{{ old('urlDK') }}" class="form-control" placeholder="{{ __('web.link_url') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <i class="fas fa-globe-europe"></i>
                    </div>
                </div>
            @if ($errors->has('urlDK'))
                <span class="text-danger">{{ $errors->first('urlDK') }}</span>
            @endif
            
            </div>
          </div>
            
          <div class="modal-footer justify-content-between">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('keywords.close') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('keywords.save') }}</button>
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
        },
        url: {
            required: true
        }
    },
    messages: {
        title: {
            required: "{{ __('web.please_enter_link_title') }}."
        },
        url: {
            required: "{{ __('web.please_enter_link_url') }}."
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

@stop