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
            <h1>{{ __('card.cards') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ Route('dashboard',session('business_name')) }}">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('card.cards') }}</li>
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
                <h3 class="card-title">{{ __('card.cards_list') }}</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="datatable" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>{{ __('card.card_name') }}</th>
                    <th>{{ __('card.expiry_date') }}</th>
                    <th>{{ __('card.customer') }}</th>
                    <th>{{ __('card.clips_used') }}</th>
                    <th>{{ __('card.clips_available') }}</th>
                    <th>{{ __('card.card_type') }}</th>
                    <th>{{ __('card.buy_clips') }}</th>
                    <th>{{ __('card.status') }}</th>
                    <th>{{ __('card.action') }}</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($cards as $key => $value)
                        <tr>
                            <td>{{ $value->name }}</td>
                            <td class="{{ $value->expiry_date < date('Y-m-d') ? 'passed' : '' }}">{{ \Carbon\Carbon::parse($value->expiry_date)->format($dateFormat->value)}} </td>
                            <td>{{ $value->user->name }} ({{ $value->user->email }})</td>
                            <td>{{ $value->clipUsed->sum('amount') }}</td>
                            <td class="clips">{{ $value->clips ? $value->clips : '0' }}</td>
                            <td>{{ $value->type == 1 ? __('card.treatment') : __('card.event') }}</td>
                            <td>
                                <form id="form-{{$value->id}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $value->id }}">
                                    <input type="number" class="form-control" name="purchase" placeholder="{{ __('card.purchase_clips') }}">
                                </form>
                            </td>
                            <td class='text-center'>
                                <span class="badge bg-{{ $value->is_active ? 'success' : 'danger' }}">
                                    {{ $value->is_active ? __('card.active') : __('card.deactive') }}
                                </span>
                            </td>
                            <td class='text-center'>
                            @if($value->is_active)
                              @can('Card Clip Puchase')
                                <button class="btn btn-success btn-sm mt-1" data-form-id="#form-{{ $value->id }}" onclick="AddClips(this)">
                                    <i class="nav-icon fas fa-shopping-cart"></i>
                                    {{ __('card.buy') }}
                                </button>
                              @endcan  
                            @endif 
                            @can('Card Edit')   
                                <a class="btn btn-info btn-sm mt-1" href="{{ Route('editCard',array(session('business_name'),md5($value->id))) }}">
                                    <i class="nav-icon fas fa-edit"></i>
                                    {{ __('keywords.edit') }}
                                </a>
                            @endcan    
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ __('card.card_name') }}</th>
                    <th>{{ __('card.expiry_date') }}</th>
                    <th>{{ __('card.customer') }}</th>
                    <th>{{ __('card.clips_used') }}</th>
                    <th>{{ __('card.clips_available') }}</th>
                    <th>{{ __('card.card_type') }}</th>
                    <th>{{ __('card.buy_clips') }}</th>
                    <th>{{ __('card.status') }}</th>
                    <th>{{ __('card.action') }}</th>
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

@section('scripts')
<script>
@can('Card Clip Puchase')
function AddClips(obj)
{
    //--------- For notification -----
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    
    var form = jQuery(obj).attr('data-form-id');
    var current = jQuery(obj).closest('tr').find('td.clips').text();
    var add = jQuery(form).find('input[name=purchase]').val();

    $.ajax({
      type: 'POST',
      url: '/updateCardAjax',
      data: jQuery(form).serialize(),
      dataType: 'json',
      success: function (data) {
        if(data == 'success'){
            Toast.fire({
              icon: 'success',
              title: ' {{ __('card.catcs') }}'
          });
          var n = parseInt(current)+parseInt(add);
          jQuery(obj).closest('tr').find('td.clips').text(n);
          jQuery(form).find('input[name=purchase]').val('');
        }
        else{
            Toast.fire({
              icon: 'error',
              title: ' {{ __('card.tiaetacic') }}'
            });
        }
      },
      error: function (data) {
        Toast.fire({
              icon: 'error',
              title: ' {{ __('card.tiaetacic') }}'
          })
      }
    }); 
}
@endcan
</script>
@stop