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
            <h1>{{ __('survey.survey_questions') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('survey.survey_questions') }}</li>
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
                <h3 class="card-title">{{ __('survey.survey_questions_list') }}</h3>
                @can('Survey Question Create')
                <span class="float-right"><a href="{{Route('addQuestion',session('business_name'))}}" class="btn btn-success btn-sm">{{ __('survey.add_new') }}</a></span>
                @endcan
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              @can('Survey Question View')
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('survey.language') }}</th>
                    <th>{{ __('survey.question') }}</th>
                    <th>{{ __('survey.options') }}</th>                    
                    <th>{{ __('survey.status') }}</th>
                    @can('Survey Question Edit')<th>{{ __('survey.action') }}</th> @endcan                    
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($questions as $value)
                    <tr>
                        <td>
                            @foreach ( Config::get('languages') as $key => $val )
                            {{ $value->language == $key ? $val['display'] : '' }}
                            @endforeach
                            
                          </td>
                        <td>{{ $value->title }}</td>
                        <td>
                          <table class="table table-bordered">
                            <tbody>
                                @foreach($value->options->where('is_active',1) as $option)
                                <tr><th>{{ $option->value }}</th></tr>
                                @endforeach()
                            </tbody>
                          </table>
                        </td> 
                        <td class='text-center'>
                          <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">
                              {{ $value->is_active ? __('survey.active') : __('survey.deactive') }}
                          </span>
                        </td> 
                        @can('Survey Question Edit')
                        <td class='text-center'>
                          <a class="btn btn-info btn-sm mt-2" href="{{ Route('editQuestion',array(session('business_name'),md5($value->id))) }}">
                                <i class="nav-icon fas fa-edit"></i>
                                {{ __('keywords.edit') }}
                          </a>
                        </td> 
                        @endcan                      
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('survey.language') }}</th>
                    <th>{{ __('survey.question') }}</th>
                    <th>{{ __('survey.options') }}</th>                    
                    <th>{{ __('survey.status') }}</th>
                    @can('Survey Question Edit')<th>{{ __('survey.action') }}</th> @endcan                    
                  </tr>
                  </tfoot>
                </table>
              @endcan  
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