@extends('layouts.backend')

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('emails.email') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('emails.email') }}</li>
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
                <h3 class="card-title">{{ __('emails.email') }}</h3> 
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                
                <form action="#" method="post" id="saveEmail">
                    @csrf

                    <div class="input-group mb-3"><b>{{ __('keywords.to') }}:&nbsp;&nbsp;&nbsp;&nbsp;</b>
                        @if(empty($users))
                        {{ __('emails.all') }}
                        @else
                            @foreach($users as $user)
                                {{$user->email}}
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <div class="input-group  mb-3">
                    <b>{{ __('emails.time') }}:&nbsp;&nbsp;&nbsp;&nbsp;</b>
                        {{\Carbon\Carbon::parse($email->schedule)->format($dateFormat->value.' H:i')}}
                    </div>

                    <div class="input-group mb-3">
                    <b>{{ __('emails.subject') }}:&nbsp;&nbsp;&nbsp;&nbsp;</b>
                        {{$email->subject}}
                    </div>

                    <div class="input-group">
                    <b>{{ __('emails.message') }}:&nbsp;&nbsp;&nbsp;&nbsp;</b>
                        {!! $email->content !!}
                    </div>
                    </div>
                </form>
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
jQuery(function(){
    jQuery('.textarea').summernote({
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
    });

    //Date range picker
    $('#reservationdate').datetimepicker({
        // format: 'LT'
    });

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });

});
</script>

@stop