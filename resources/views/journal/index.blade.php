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
            <h1>{{ __('journal.journalusers') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('journal.journalusers') }}</li>
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
                <h3 class="card-title">{{ __('journal.users_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('journal.name') }}</th>
                    <th>{{ __('journal.email') }}</th>
                    <th>{{ __('journal.number') }}</th>
                    <th>{{ __('journal.up_to_date') }}</th>
                    @can('Journal Open')
                    <th>{{ __('journal.action') }}</th>
                    @endcan
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($users as $key => $value)
                    <tr>
                        <td>{{ $value->name }}</td>
                        <td class='text-center'>{{ $value->email }}</td>
                        <td>{{ $value->number }}</td>
                        <td class='text-center'>
                        @foreach($value->LastTreatment as $date)
                          {{ \Carbon\Carbon::parse($date->created_at)->format($dateFormat->value) }}
                          @php break; @endphp
                        @endforeach
                        </td>
                        @can('Journal Open')
                        <td class='text-center'>
                            <a class="btn btn-info btn-sm mt-2" href="{{ Route('journal',array(session('business_name'),md5($value->id))) }}">
                                <i class="nav-icon fas fa-edit"></i>
                                {{ __('journal.open_journal') }}
                            </a>
                        </td>
                        @endcan
                    </tr>
                  @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('journal.name') }}</th>
                    <th>{{ __('journal.email') }}</th>
                    <th>{{ __('journal.number') }}</th>
                    <th>{{ __('journal.up_to_date') }}</th>
                    @can('Journal Open')
                    <th>{{ __('journal.action') }}</th>
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