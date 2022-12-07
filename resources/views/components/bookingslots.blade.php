@php 
    $cardIs = 0;
    $total = count($range);
    $i = 0;
    if(is_numeric(\Auth::user()->role)){
        $rolee = App\Models\Role::find(\Auth::user()->role);
        $role = $rolee->title;
    }
    else{
        $role = \Auth::user()->role;
    }

    // $escape = 0;
    // $minSlots = 100;
    // foreach ($treatments as $treatment){
    //     if($minSlots > ceil($treatment->inter/$interval))
    //         $minSlots = ceil($treatment->inter/$interval);
    // }
@endphp

@foreach($range as $time)
@php   $i++;  @endphp

    @if( $i < $total )
    
        <tr>
            @if( isset($bookingDetails[$time]) )
                @if( $bookingDetails[$time]['status'] == 'Lunch' || $bookingDetails[$time]['status'] == 'Break' )
                    @php 
                        $id = $bookingDetails[$time]['id'];
                        $number = '';
                        $name = '';
                        $email = '';
                        $comment = '';
                        $book = '';
                        $pause = '';
                        $bookedTime = '';
                        $treatmentsS = 0;
                        $departmentS = 0;
                        $cardIs = 0;
                        $treatmentID = '';
                        $card = Null;
                        $consultingBtn = __('treatment.'.$bookingDetails[$time]['status']);

                        if( $role == 'Super Admin' && $bookingDetails[$time]['status'] == 'Break'){
                            $pause = '<button type="submit" class="btn btn-info" data-time="'.$time.'" data-id="'.$id.'" onclick="deletePause(this)">'.__('treatment.delete_pause').'</button>';
                        }

                        $class = 'bg-warning';  
                    @endphp
                @elseif( $bookingDetails[$time]['status'] == 'Booked' )
                    @php 

                        $id = $bookingDetails[$time]['id'];
                        $user = App\Models\User::find($bookingDetails[$time]['user_id']);
                        $number = "<a href='Tel:".$user->number."'>".$user->number."</a>";
                        $name = $user->name;
                        $email = $user->email;
                        $comment = $bookingDetails[$time]['comment'];
                        $book = '<button type="submit" class="btn btn-danger" data-time="'.$time.'" data-id="'.$id.'" onclick="deleteBooking(this)">'.__('treatment.delete_booking').'</button>';
                        $bookedTime = $bookingDetails[$time]['bookedtime'];

                        $bookingCount = $user->bookings->count();
                        $dot = $btntxt = '';

                        if( $bookingDetails[$time]['payment'] == NULL || $bookingDetails[$time]['part'] == NULL){
                            $dot = '<i class="fas fa-circle" style="color:green;"></i> ';
                            $btntxt = __('treatment.start_consultation');
                        }
                        else{
                            switch ($bookingDetails[$time]['payment']) {
                                case 'Absence':
                                    $dot = '<i class="fas fa-circle" style="color:#ff0707;"></i> ';
                                    break;
                                case 'Late Cancellation':
                                    $dot = '<i class="fas fa-circle" style="color:#d16417;"></i> ';
                                    break;    
                                default:
                                    $dot = '<i class="fas fa-circle" style="color:#ffee07;"></i> ';
                                    break;
                            }
                            $btntxt = __('treatment.treatment_finished');
                        }                               

                        // if( $bookingDetails[$time]['payment'] == NULL || $bookingDetails[$time]['part'] == NULL){
                        //     $dot = '<i class="fas fa-circle PaymentPendingDot"></i> ';
                        //     $btntxt = __('treatment.start_consultation');
                        // }
                        // else{
                        //     switch ($bookingDetails[$time]['payment']) {
                        //         case 'Absence':
                        //             $dot = '<i class="fas fa-circle AbsenceDot"></i> ';
                        //             break;
                        //         case 'Late Cancellation':
                        //             $dot = '<i class="fas fa-circle LateCancellationDot"></i> ';
                        //             break;    
                        //         default:
                        //             $dot = '<i class="fas fa-circle PaymentDoneDot"></i> ';
                        //             break;
                        //     }
                        //     $btntxt = __('treatment.treatment_finished');
                        // }      

                        $consultingBtn = '<a data-user-id="'.md5($user->id).'" class="btn btn-default btn-sm" href="'.Route('journal',array(session('business_name'),md5($user->id))).'">'.$dot.'&nbsp;'.$btntxt.'</a>';

                        if($bookingCount == 1 && $bookingDetails[$time]['payment'] == NULL && $bookingDetails[$time]['part'] == NULL){
                            $consultingBtn = '<a  href="'.Route('journal',array(session('business_name'),md5($user->id))).'" data-user-id="'.md5($user->id).'" class="btn btn-warning btn-sm">'.__('treatment.start_consultation').'<br>('.__('treatment.new_customer').')</a>';
                        }
                        
                        #if payment option not selected
                        if( $mobilePaySetting != NULL ){
                            
                            if( $bookingDetails[$time]['payment'] == 'Mobilepay' ) 
                                $mptext = '<i class="fa fa-check magin-color-icon"></i>'; 
                            else  
                                $mptext = '';    


                            if( $mobilePaySetting->value == 'true' && $bookingDetails[$time]['price'] > 0)
                            $consultingBtn .= '<a href="javascript:;" data-slot-id="'.$bookingDetails[$time]['id'].'" class="btn btn-sm btn-default btn-block mt-1" onclick="sendMobilePayReq(this)">'.$mptext.'<img src="/images/mobilepay.svg" alt="MobilePay" width="100"></a>'; 
                        }

                        #if payment and treatment part selected the give option to send receipt
                        // if($receiptOption != NULL && $receiptOption->value == 'true'){
                        //     if($bookingDetails[$time]['payment'] != NULL && $bookingDetails[$time]['part'] != NULL){
                        //         $markcheck = '';
                        //         if($bookingDetails[$time]['receipt'] != NULL){
                        //             $markcheck = '<i class="fas fa-check"></i>&nbsp;&nbsp;';
                        //             $consultingBtn .= '<a  href="'.Route('view-receipt',array(session('business_name'),str_replace("\-","-",$bookingDetails[$time]['receipt']->data))).'" target="_blank" class="btn btn-info btn-sm btn-default btn-block mt-1"><i class="fas fa-file"></i>&nbsp;&nbsp;'.__('treatment.view_receipt').'</a>';
                        //         }
                        //         $consultingBtn .= '<a  href="'.Route('receipt-send',array(session('business_name'),md5($bookingDetails[$time]['id']))).'" class="btn btn-warning btn-sm btn-default btn-block mt-1">'.$markcheck.__('treatment.send_receipt').'</a>';
                        //     }
                        // }

                        $card = Null;
                        if($bookingDetails[$time]['clips'] != Null){
                            $clipId = $bookingDetails[$time]['clips']->id;
                            $card = App\Models\Card::find($bookingDetails[$time]['card']);
                        }

                        $treatmentID = $bookingDetails[$time]['treatment_id'];
                        $pause = '';
                        $treatmentsS = 2;
                        $departmentS = 2;
                        $cardIs = 1;

                        $class = 'bg-info'; 
                    @endphp
                    @if ($bookingDetails[$time]['parent'] != NULL)
                        @continue
                    @endif
                @endif 
                @php 
                    echo '<td class="'.$class.' text-center status"><form id="form'.str_replace(':','',$time).$dateID.'" ><input type="hidden" name="date_id" value="'.$dateID.'"><input type="hidden" name="data_time" value="'.$time.'"><input type="hidden" name="user_id" value="'.$bookingDetails[$time]['user_id'].'"></form><span>'.$consultingBtn.' </span>';
                @endphp   
            @else
                @php 
                    // if($escape > 0){
                    //     $escape--;
                    //     continue;
                    // }

                    // $key = array_search($time, $range);
                    // for($j=1; $j<$minSlots; $j++){
                    //     if(array_key_exists($key+$j,$range) ){
                    //         $next = $range[$key+$j];
                    //         if( !array_key_exists($next,$bookingDetails) ){
                    //             $escape = $minSlots-1;
                    //         }else{
                    //             $escape = 0;
                    //             break;
                    //         }
                    //     }else{
                    //         $escape = 0;
                    //         break;
                    //     }
                    // }
                
                //---- check here which slot need to show and which one not
                    $id = '';
                    $number = '<input type="text" class="form-control" name="number" placeholder="'.__('keywords.number').'" data-time="'.$time.'" date-id="'.$dateID.'" onkeyup="suggestUser(this)" autocomplete="off">';

                    $name = '<input type="text" class="form-control" name="name" placeholder="'.__('keywords.name').'" data-time="'.$time.'" date-id="'.$dateID.'" onkeyup="suggestUser(this)" autocomplete="off"><ul class="list-group p-a" id="name'.str_replace(':','',$time).$dateID.'"></ul>';

                    $email = '<input type="email" class="form-control" name="email" placeholder="'. __('keywords.email').'" data-time="'.$time.'" date-id="'.$dateID.'" onkeyup="suggestUser(this)" autocomplete="off">';

                    $comment = '<input type="text" class="form-control" name="comment" placeholder="'.__('treatment.comment').'" data-time="'.$time.'" >';
                    
                    $treatmentsS = 1;
                    $departmentS = 1;
                    $cardIs = 0;
                    $card = Null;
                    $book = '<button type="submit" class="btn btn-info" data-time="'.$time.'" date-id="'.$dateID.'" onclick="bookSlot(this)">'.__('treatment.book').'</button>';
                    $bookedTime = '';
                    $pause = '';
                    $treatmentID = '';

                    if( $role == 'Super Admin' ){
                        $pause = '<button type="submit" class="btn btn-warning" data-time="'.$time.'" date-id="'.$dateID.'" onclick="timePause(this)">'.__('treatment.close_time').'</button>';
                    }
                    echo '<td class="bg-success text-center status"><form id="form'.str_replace(':','',$time).$dateID.'" ><input type="hidden" name="date_id" value="'.$dateID.'"><input type="hidden" name="data_time" value="'.$time.'"></form><span>'.__('treatment.available').'</span>';
                @endphp
            @endif    
            </td>
            <td class="text-center time">{{ $time }}</td>
            <td class="name" style="min-width:200px;">{!! $name !!}</td>
            <td class="email" style="min-width:200px;">{!! $email !!}</td>
            <td class="number" style="min-width:200px;">{!! $number !!}</td>
            @if($cardIs)

            <td class="text-center clipCard">
                <select name="card" class="form-control select2" data-time="{{$time}}" onchange="cardSelect(this)">
                    <x-cards-list type="1" card="{{$card != Null ? $card->id : $card }}" user="{{$bookingDetails[$time]['user_id']}}" />
                </select> 
                </td>    
            @else
            <td class="clipCard"></td>
            @endif
            <td class="text-center cutBack"> @if( $card != Null ) {{$card->clips}} @endif</td>
            <td class="text-center cutCard" data-id="{!! $id !!}" data-treatment-id="{!! $treatmentID !!}">
            @if( $card != Null )
            <button class="btn btn-warning btn-sm"  onclick="cutBackClips(this)" data-clip-id="{{$clipId}}">{{ __('treatment.undo_clips_in_clipboad') }}</button>
            @endif
            </td>
            <td class="department">
                @if( $departmentS == 1 )
                    <select name="department" class="form-control select2" data-time="{{$time}}" >
                        <x-departments-list />
                    </select>    
                @elseif( $departmentS == 2 )
                    {{ $bookingDetails[$time]['department'] }}               
                @endif 
            </td>
            <td class="treatment">
            @if( $treatmentsS == 1 )
                <select name="treatment" class="form-control select2" data-time="{{$time}}" >
                    <x-treatments-list :treatments="$treatments" />
                </select>    
            @elseif( $treatmentsS == 2 )
                @if($bookingDetails[$time]['payment'] == NULL && $bookingDetails[$time]['part'] == NULL && $bookingDetails[$time]['clips'] == Null)
                    <a href="javascript:;" title="{{ __('keywords.click_to_change') }}" data-id="{{$id}}" onclick="getAvailableTreatments(this)">{{ $bookingDetails[$time]['treatment'] }}</a>  
                @else
                    {{ $bookingDetails[$time]['treatment'] }}
                @endif                           
            @endif    
            </td>
            <td class="text-center bookTime">{!! $bookedTime !!}</td>
            <td class="comment" style="min-width:200px;">{!! $comment !!}</td>
            <td class="text-center bookDel">@can('Date Book'){!! $book !!}@endcan</td>
           <td class="text-center pauseUnpause">{!! $pause !!}</td>
        </tr>
    @endif
@endforeach

@if ( $waitingList->waiting_list == 1)
    @php
    $waitingLists = App\Models\TreatmentWaitingSlot::where('date_id',$dateID)->get();
    @endphp

    @foreach ($waitingLists as $waiting)
    <tr>
        <td class="bg-dark text-center status"><form><input type="hidden" name="date_id" value="{{$waiting->date_id}}"><input type="hidden" name="data_time" value="00:00"><input type="hidden" name="user_id" value="{{ $waiting->user_id }}"></form><span>{{ __('treatment.waiting_list') }}</span></td>
        <td class="text-center time">00:00</td>
        <td class="name" style="min-width:200px;">{{ $waiting->user->name }}</td>
        <td class="email" style="min-width:200px;">{{ $waiting->user->email }}</td>
        <td class="number" style="min-width:200px;"><a href='Tel:{{ $waiting->user->number }}'>{{ $waiting->user->number }}</a></td>
        <td class="clipCard"></td>
        <td class="text-center cutBack"></td>
        <td class="text-center cutCard"></td>
        <td class="department"></td>
        <td class="treatment"></td>
        <td class="text-center bookTime"></td>
        <td class="comment" style="min-width:200px;">{!! $waiting->comment !!}</td>
        <td class="text-center bookDel">
            @if( $role == 'Super Admin' )
                <button type="submit" class="btn btn-danger" data-time="00:00" data-id="{{$waiting->id}}" onclick="waitingSlotDelete(this)">{{__('treatment.delete_booking')}}</button>
            @endif
        </td>
        <td class="text-center pauseUnpause"></td>
    </tr>

    @endforeach

@endif

@if ( count($bookingDetails) == (count($range)-1) && $waitingList->waiting_list == 1)
    <tr>
        <td class="bg-secondary text-center status"><form id="form0000{{$dateID}}" ><input type="hidden" name="date_id" value="{{$dateID}}"><input type="hidden" name="data_time" value="00:00"></form><span>{{__('treatment.waiting_list')}}</span></td>
        <td class="text-center time">00:00</td>
        <td class="name" style="min-width:200px;">
            <input type="text" class="form-control" name="name" placeholder="{{__('keywords.name')}}" data-time="00:00" date-id="{{ $dateID }}" onkeyup="suggestUser(this)" autocomplete="off"><ul class="list-group p-a" id="name0000{{$dateID}}"></ul>
        </td>
        <td class="email" style="min-width:200px;">
            <input type="email" class="form-control" name="email" placeholder="{{ __('keywords.email')}}" data-time="00:00" date-id="{{$dateID}}" onkeyup="suggestUser(this)" autocomplete="off">
        </td>
        <td class="number" style="min-width:200px;">
            <input type="text" class="form-control" name="number" placeholder="{{__('keywords.number')}}" data-time="00:00" date-id="{{$dateID}}" onkeyup="suggestUser(this)" autocomplete="off">
        </td>
        @if($cardIs)
            <td class="text-center clipCard"></td>    
        @else
            <td class="clipCard"></td>
        @endif
        <td class="text-center cutBack"></td>
        <td class="text-center cutCard"></td>
        <td class="department">
            <select name="department" class="form-control select2" data-time="00:00" >
                <x-departments-list />
            </select>    
        </td>
        <td class="treatment">
            <select name="treatment" class="form-control select2" data-time="00:00" >
                <x-treatments-list :treatments="$treatments" />
            </select>    
        </td>
        <td class="text-center bookTime"></td>
        <td class="comment" style="min-width:200px;">
            <input type="text" class="form-control" name="comment" placeholder="{{__('treatment.comment')}}" data-time="00:00" >
        </td>
        <td class="text-center bookDel">
            @can('Date Book')
                <button type="submit" class="btn btn-info" data-time="00:00" date-id="{{$dateID}}" onclick="bookWaitingSlot(this)">{{__('treatment.book')}}</button>
            @endcan</td>
        <td class="text-center pauseUnpause"></td>
    </tr>
@endif

