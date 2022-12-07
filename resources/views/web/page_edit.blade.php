@extends('layouts.backend')

@section('content')

<div class="content-wrapper">
    <form name="pageUpdate" method="POST" id="pageUpdate" action="{{ Route('savePage',session('business_name')) }}">
    @csrf
   
    <input type="hidden" name="name" value="@foreach($pages as $page) {{ $page->page }} @break @endforeach">
    <br>
    <!-- Main content -->
    <section class="content">
      <div class="">
        <div class="col-md-12">
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">
                @foreach($pages as $page) 
                  @if (Lang::locale() == $page->language)
                    {{ $page->title ?: $page->page }} 
                  @endif
                @endforeach
              </h3>
              <!-- tools box -->
              <div class="card-tools">
                <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse" data-toggle="tooltip"
                        title="Collapse">
                  <i class="fas fa-minus"></i></button>
              </div>
              <!-- /. tools -->
            </div>
            <!-- /.card-header -->
            <div class="card-body pad">
                @foreach ( Config::get('languages') as $key => $val )
                    @if( $pages->where('language', $key) )
                     @php
                       $page = $pages->where('language', $key)->first();
                     @endphp
                      <div class="mb-3">
                        <label>{{ __('web.language') }}:</label>
                        <input type="hidden" name="language[]" value="{{ $key }}">
                        <input type="text" class="form-control" value="{{ $val['display'] }}" disabled>
                      </div>
                      <div class="mb-3">
                        <label>{{ __('web.page_title') }}:</label>
                        <input type="text" class="form-control" name="title[]" value="{{ $page ? $page->title : '' }}">
                      </div>
                      <div class="mb-3">
                        <label>{{ __('web.page_content') }}:</label>
                        @if(stripos($page->page,"Link-") === FALSE)
                          <textarea class="textarea" name="content[]" placeholder="{{ __('web.place_content_here') }}"
                                    style="width: 100%; height: 300px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
                                    {{ $page ? $page->content : '' }}
                          </textarea>
                        @else
                          <input type="text" class="form-control" name="content[]" value="{{ $page ? $page->content : '' }}">
                        @endif
                      </div>
                    @endif
                @endforeach 
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="status" class="custom-control-input" id="exampleCheck1" 
                        @foreach($pages as $page) {{ $page->is_active ? 'checked' : '' }} @break @endforeach
                        
                        >
                        <label class="custom-control-label" for="exampleCheck1">{{ __('web.enable_for_menu') }}</label>
                    </div>
                </div>  
                <div class="form-group">
                    <div class="custom-control text-center">
                        <button type="submit" class="btn btn-info btn-lg">{{ __('keywords.save') }}</button>
                    </div>
                </div> 
              
            </div>
          </div>
        </div>
        <!-- /.col-->
      </div>
      <!-- ./row -->
    </section>
</div>    
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


	<!-- Summernote -->
	<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
	<script>
	  $(function () {
		// Summernote
		$('.textarea').summernote({
        height: 300,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'insert', [ 'link'] ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
        ]
    })
	  })
	</script>

@stop