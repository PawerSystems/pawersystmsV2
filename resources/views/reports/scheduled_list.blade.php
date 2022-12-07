@extends('layouts.backend')

@section('content')
<style>
.passed{ color:red; }
</style>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>{{ __('reports.list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('reports.list') }}</li>
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
                <h3 class="card-title">{{ __('reports.list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('reports.scheduled_by') }}</th>
                    <th>{{ __('reports.scheduled_for') }}</th>
                    <th>{{ __('reports.duration') }}</th>
                    <th>{{ __('reports.schedule_period') }}</th>
                    <th>{{ __('reports.schedule_time') }}</th>
                    <th>{{ __('reports.report_type') }}</th>
                    <th>{{ __('reports.last_run') }}</th>
                    <th>{{ __('profile.status') }}</th>
                    <th>{{ __('reports.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($reports as $report)
                    @php
                        $ids = explode(',',$report->users);
                        $users = App\Models\User::select('name')->whereIn('id',$ids)->pluck('name')->toArray();
                        $names = implode(' | ',$users);
                    @endphp
                        <tr>
                            <td>{{ $report->user->name }}</td>
                            <td>{{ $names  }}</td>
                            <td>{{ $report->duration }}</td>
                            <td>{{ $report->period  == 'end' ? __('reports.month_end') : $report->period  }}</td>
                            <td>{{ $report->time  }}</td>
                            <td>{{ ucfirst($report->type) }}</td>
                            <td>{{ $report->last_run ? \Carbon\Carbon::parse($report->last_run)->format($dateFormat->value.' H:i:s') : '--' }}</td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $report->is_active ? 'success' : 'danger' }}">
                                    {{ $report->is_active ? __('profile.active') : __('profile.deactive') }}
                                </span>
                            </td>
                            <td><a href="{{ Route('report-edit',[session('business_name'),md5($report->id)]) }}" class="btn btn-info">{{ __('keywords.edit') }}</a></td>
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
@stop