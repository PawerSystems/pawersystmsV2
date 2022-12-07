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
            <h1>{{ __('profile.cards_list') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('profile.cards_list') }}</li>
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
                <h3 class="card-title">{{ __('profile.cards_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('card.card_name') }}</th>
                    <th>{{ __('card.expiry_date') }}</th>
                    <th>{{ __('card.clips_used') }}</th>
                    <th>{{ __('card.clips_available') }}</th>
                    <th>{{ __('card.card_type') }}</th>
                    <th>{{ __('profile.status') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($cards as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            <td class="{{ $value->expiry_date < date('Y-m-d') ? 'passed' : '' }}">{{ \Carbon\Carbon::parse($value->expiry_date)->format($dateFormat)}} </td>
                            <td>{{ $value->clipUsed->sum('amount') }}</td>
                            <td class="clips">{{ $value->clips ? $value->clips : '0' }}</td>
                            <td>{{ $value->type == 1 ? __('card.treatment') : __('card.event') }}</td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">
                                    {{ $value->is_active ? __('profile.active') : __('profile.deactive') }}
                                </span>
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
@stop