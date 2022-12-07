<style>
.calenderTableRow{
    min-width:1000px;
}
span.select2{
    width:100% !important;
}
.open{
    background:LimeGreen;
    color:white;
    border:1px solid white;
    text-align:center;
    cursor:pointer;
}
.day{
    background:black;
    color:white;
    text-align:center;
}
.booked{
    background:lightgray;
    border:1px solid white;
    text-align:center;
}
.break, .lunch {
    background:#ffc107 !important;
    border:1px solid white;
    text-align:center;
}
@media( max-width:768px ){
    .btn-info{ margin: 2px 0 0 0; }
}
</style>
<script>
    var dates = [];
</script>
@foreach($dates as $date)
<script>
    dates.push({{ $date->id }});
</script>    
@endforeach
<div class="container-fluid calenderTable" style="display:none;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body table-responsive p-0" style="max-height:400px;">
             
                    <div class="calenderTableRow row">
                    @foreach($dates as $date)
                        <div class="col-2">
                            <div class="col-12 day">
                                {{  __('keywords.'.\Carbon\Carbon::parse($date->date)->format('l').'') }} 
                                <br> 
                                {{ \Carbon\Carbon::parse($date->date )->format($dateFormat->value)}}
                                <br>
                                {{ ($date->description ? $date->description : '---------') }}
                            </div>
                                
                            <div  id="tr-{{ $date->id }}"></div>
                        </div> 
                    @endforeach 
                    </div>
                    
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="">
        <div class="card">
            <div class="card-body">
                <button class="btn btn-info show_all">{{ __('treatment.show_all') }}</button>
                <button class="btn btn-info show_all_free">{{ __('treatment.show_all_free') }}</button>
                <a href="{{ Route('treatmentbookings',[session('business_name'),md5(Auth::user()->id)]) }}" class="btn btn-info">{{ __('treatment.show_my_spots') }}</a>
                {{-- <a href="{{ Route('treatmentbookings',session('business_name')) }}" class="btn btn-info">{{ __('treatment.show_all_spots') }}</a> --}}
            </div>
        </div>
    </div>
</div>            

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <nav aria-label="Contacts Page Navigation">
                <ul class="pagination justify-content-center m-0">
                {{ $dates->links() }} 
                </ul>
            </nav>
        </div>
    </div> 
</div>
@foreach($dates as $date)

<div class="col-md-12" id="table-{{$date->id}}">
    <div class="card">
        <div class="card-header">
        <div class="card-tools">
            <button type="button" class="btn bg-default btn-sm" data-card-widget="collapse">
            <i class="fas fa-minus"></i>
            </button>
        </div>
        <h4> 
            {{  __('keywords.'.\Carbon\Carbon::parse($date->date)->format('l').'') }} {{ \Carbon\Carbon::parse($date->date )->format($dateFormat->value)}}
        </h4>
        <h4 class="card-title">{{ __('treatment.therapist') }}: <b>{{ $date->user ? $date->user->name : 'N/A' }}</b></h4><br>  
        <h4 class="card-title">{{ __('treatment.treatments') }}:</h4> 
        <p>
            @foreach($date->treatments as $treat)
            <b>{{ $treat->treatment_name }}</b> ({{ $treat->time_shown ?: $treat->inter }} min) 
            @endforeach
        </p>              
        </div>
        <!-- /.card-header -->
        <div class="card-body table-responsive p-0 overlay-wrapper parent-div">
        <table class="table table-hover table-bordered text-nowrap">
            <thead>
                <tr class="dark">
                    <th>{{ __('treatment.status') }}</th>
                    <th>{{ __('treatment.time') }}</th>
                    <th>{{ __('keywords.name') }} </th>
                    <th>{{ __('keywords.email') }}  </th>
                    <th>{{ __('keywords.number') }} </th>
                    <th class="clipCard">{{ __('treatment.cards') }} </th>
                    <th class="cutBack">{{ __('treatment.cut_back') }}</th>
                    <th class="cutCard">{{ __('treatment.cut_card') }}</th>
                    <th class="department">{{ __('treatment.department') }}</th>
                    <th>{{ __('treatment.treatment') }}</th>
                    <th>{{ __('treatment.ordered') }}</th>
                    <th>{{ __('treatment.comment') }}</th>
                    <th>{{ __('treatment.book_delete') }} </th>
                    <th>{{ __('treatment.pause') }}</th>
                </tr>
            </thead>
            <tbody>
            @if( $date->treatmentSlots->count() > 0 )
                <x-bookingslots :treatments="$date->treatments" :booked="$date->treatmentSlots" start="{{ $date->from }}" end="{{ $date->till }}"  dateID="{{ $date->id }}" />
            @else 
                <x-bookingslots :treatments="$date->treatments" :booked="[]" start="{{ $date->from }}" end="{{ $date->till }}" dateID="{{ $date->id }}" />
            @endif   
            </tbody>
        </table>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
</div>
@endforeach
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <nav aria-label="Contacts Page Navigation">
                <ul class="pagination justify-content-center m-0">
                {{ $dates->links() }} 
                </ul>
            </nav>
        </div>
    </div> 
</div>


