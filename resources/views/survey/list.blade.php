@extends('layouts.backend')

@section('content')
<style>
.table td, .table th{
  vertical-align: inherit;
}
.card-body.p-0 .table tbody>tr>td:first-of-type, .card-body.p-0 .table tbody>tr>th:first-of-type, .card-body.p-0 .table thead>tr>td:first-of-type, .card-body.p-0 .table thead>tr>th:first-of-type{
  padding-left: 0.7rem !important;
}
ul.p-a{ position:absolute; z-index:99; }
ul.p-a li{ cursor:pointer; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('survey.survey') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('survey.survey') }}</li>
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
                <h3 class="card-title">{{ __('survey.survey_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('survey.date') }}</th>
                    <th>{{ __('keywords.name') }}</th>
                    <th>{{ __('keywords.email') }}</th>
                    <th>{{ __('survey.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($surveys as $key => $value)
                    <tr>
                      <td>{{ \Carbon\Carbon::parse($value->created_at)->format($dateFormat->value.($timeFormat->value == 12 ? ' h:i:s a' : ' H:i:s'))}}</td>
                      <td>{{ $value->name }}</td>
                      <td>{{ $value->email }}</td> 
                      <td class='text-center'><button class="btn btn-success" data-name="{{ $value->name }}" data-email="{{ $value->email }}" data-date="{{ \Carbon\Carbon::parse($value->created_at)->format($dateFormat->value)}}" data-id="{{ $value->id }}" onclick="showSurvey(this)">{{ __('keywords.view') }}</button></td>
                  </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('survey.date') }}</th>
                    <th>{{ __('keywords.name') }}</th>
                    <th>{{ __('keywords.email') }}</th>
                    <th>{{ __('survey.action') }}</th>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('keywords.close') }}</button>
      </div>
    </div>
  </div>
</div>
@stop

@section('scripts')
<script>


function showSurvey(obj){
  const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
  });

  var id = jQuery(obj).attr('data-id');
  var name = jQuery(obj).attr('data-name');
  var email = jQuery(obj).attr('data-email');
  var date = jQuery(obj).attr('data-date');

  if(name != '' || email != '')
    jQuery('.modal-title').html('<b>'+name+'<b>'+' - '+email+'<br>'+date);
  else
    jQuery('.modal-title').text('{{ __('survey.anonymous') }}');

  var token = $('meta[name="csrf-token"]').attr('content');
  $.ajax({
    type: 'POST',
    url: '/showSurvey',
    data: { 'id':id,'_token':token },
    success: function (data) {
      jQuery('.modal-body').html(data);
      $('#exampleModal').modal('show');
    },
    error: function (data) {
      Toast.fire({
            icon: 'error',
            title: '{{ __('survey.tiauetsd') }}'
        })
    }
  });


}

</script>
@stop