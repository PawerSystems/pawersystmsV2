@extends('layouts.backend')

@section('content')

@php $cpr = $mobilePay =  $CPRForInsurance = $mdr = 0; @endphp
    @foreach($settings as $setting)

        @if($setting->key == 'cpr_emp_fields')
            @if($setting->value == 'true')
                @php $cpr = 1; @endphp
            @else
                @php $cpr = 0; @endphp
            @endif 
        @endif

        @if($setting->key == 'mdr_field')
            @if($setting->value == 'true')
                @php $mdr = 1; @endphp
            @else
                @php $mdr = 0; @endphp
            @endif 
        @endif

        @if($setting->key == 'mobile_pay')
            @if($setting->value == 'true')
                @php $mobilePay = 1; @endphp
            @else
                @php $mobilePay = 0; @endphp
            @endif 
        @endif

        @if($setting->key == 'cpr_emp_fields_insurance')
        @if($setting->value == 'true')
            @php $CPRForInsurance = 1; @endphp
        @else
            @php $CPRForInsurance = 0; @endphp
        @endif 
    @endif

    @endforeach
<style>
.btn { min-width:150px; }
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
            <h1>{{ __('treatment.old_treatment_dates') }}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">{{ __('keywords.home') }}</a></li>
              <li class="breadcrumb-item active">{{ __('treatment.old_treatment_dates') }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content treatment">
      <div class="container-fluid">
        <div class="row">
          <x-bookingtable dimention='<' />
        </div>
      </div>  
    </section>    
  </div>  

  @can('Customer Create')
  <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('treatment.add_new_customer') }}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>
      <div class="modal-body">

      <form method="post" id="saveCustomer">                    
            @csrf
            <input type="hidden" name="formId" id="formId">
            <div class="input-group mb-3">
                <input type="text" name="cname" value="{{ old('cname') }}" class="form-control" placeholder="{{ __('treatment.full_name') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-user"></span>
                    </div>
                </div>
              @if ($errors->has('cname'))
                  <span class="text-danger">{{ $errors->first('cname') }}</span>
              @endif
            </div>

            <div class="input-group mb-3">
                <input type="email" name="cemail" value="{{ old('cemail') }}" class="form-control" placeholder="{{ __('keywords.email') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
              @if ($errors->has('cemail'))
                  <span class="text-danger">{{ $errors->first('cemail') }}</span>
              @endif
            </div>

            <!-- <span class="text-info"><i>{{ __('keywords.please_did_not_remove_country_code') }}</i></span> -->
            <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <select name="ccountry" class="form-control" style="max-width:150px;">
                  <option value="45">Denmark</option>
                    @foreach($countries as $country)
                      <option value="{{ $country->id }}" @if( $country->name == 'Denmark') selected @endif>{{ $country->name }}</option>
                    @endforeach 
                  </select>
                </div>
                <input type="number" name="cnumber" value="{{ old('cnumber') }}" class="form-control" placeholder="{{ __('keywords.number') }}">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-phone"></span>
                    </div>
                </div>
              @if ($errors->has('cnumber'))
                  <span class="text-danger">{{ $errors->first('cnumber') }}</span>
              @endif
            </div>

            <div class="input-group mb-3">
                  <select class="form-control" name="clanguage" id="clanguage">
                  @foreach ( Config::get('languages') as $key => $val )
                    <option value="{{ $key }}">{{ $val['display'] }}</option>
                  @endforeach
                  </select>
                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class="fas fa-language"></span>
                      </div>
                  </div>
              </div>
              @if ($errors->has('clanguage'))
                  <span class="text-danger">{{ $errors->first('clanguage') }}</span>
              @endif

            @if($cpr)
              <div class="input-group mb-3">
                  <input type="text" name="cprnr" value="{{ old('cprnr') }}" class="form-control" placeholder="{{ __('treatment.cprnr') }}">
                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class="fas fa-key"></span>
                      </div>
                  </div>
                @if ($errors->has('cprnr'))
                    <span class="text-danger">{{ $errors->first('cprnr') }}</span>
                @endif
              </div>
            @else
              <input type="hidden" name="cprnr" value="">
            @endif

            @if($mdr)   
              <div class="input-group mb-3">
                  <input type="text" name="mednr" value="{{ old('mednr') }}" class="form-control" placeholder="{{ __('treatment.mednr') }}">
                  <div class="input-group-append">
                      <div class="input-group-text">
                          <span class="fas fa-key"></span>
                      </div>
                  </div>
                @if ($errors->has('mednr'))
                    <span class="text-danger">{{ $errors->first('mednr') }}</span>
                @endif
              </div>
            @else
              <input type="hidden" name="mednr" value="">
            @endif
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-info">{{ __('keywords.save') }}</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('keywords.close') }}</button>
      </div>
      </form>
    </div>

  </div>
</div>
@endcan


<div id="myCPRModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">{{ __('treatment.cprnr') }}</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
            <input type="number" id="cprNum" name="cprNum" value="{{ old('cprNum') }}" class="form-control" placeholder="{{ __('treatment.cprnr') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
          @if ($errors->has('cprNum'))
              <span class="text-danger">{{ $errors->first('cprNum') }}</span>
          @endif
        </div>
        <input type="hidden" id="field_name" value="" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick="validate_cpr_number()">{{ __('keywords.save') }}</button>
      </div>
    </div>

  </div>
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


<script type="text/javascript">
$(document).ready(function () {

   //--------- For notification -----
   const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });


  $.validator.setDefaults({
    submitHandler: function () {
        
      $.ajax({
        type: 'POST',
        url: '/addCustomer',
        data: jQuery("#saveCustomer").serialize(),
        dataType: 'json',
        success: function (data) {
          if(data['status'] == 'success'){
              Toast.fire({
                icon: 'success',
                title: ' {{ __('treatment.new_customer_have_been_added') }}'
              });

              //----- add values in customer fields --------
              var name = jQuery("#saveCustomer input[name=cname]").val();
              var email = jQuery("#saveCustomer input[name=cemail]").val();
              var number = jQuery("#saveCustomer input[name=cnumber]").val();
              var thisForm = jQuery("#saveCustomer input[name=formId]").val();
              
              addValues(name,email,number,thisForm,data['cid']);

              jQuery('#myModal').modal('hide');
              jQuery('input[name=cname],input[name=cemail],input[name=cnumber],input[name=cprnr],input[name=mednr],input[name=formId]').val('');
          }else if(data['status'] == 'exist'){
              Toast.fire({
                icon: 'error',
                title: ' {{ __('treatment.customer_with_email_exist') }}'
              });
          }else{
            Toast.fire({
                icon: 'error',
                title: '  {{ __('treatment.there_is_an_error_to_add_customer') }}'
              });
          }
        },  
      });

        //return true;
    }
  });

  //---------- Validation for user profile --------
  jQuery.validator.addMethod("validCpr", function(value, element) {
    return validate_cpr_number(value);
  }, "{{ __('treatment.please_enter_valid_number') }}");

  $('#saveCustomer').validate({
    rules: {
      @if($cpr)
        cprnr: {
            required: true,
            validCpr: true,
            minlength: 10,
            maxlength: 10
        },
      @endif
      @if($mdr)  
        mednr: {
            required: true
        },
      @endif
        cname: {
            required: true
        },
        cemail: {
            required: true,
            email: true,
        },
        cnumber: {
            required: true,
        },
    },
    messages: {
      @if($cpr)
        cprnr: {
            required: "{{ __('treatment.please_enter_CPR_number') }}",
            minlength: "{{ __('treatment.please_enter_at_least_10_cha') }}",
            maxlength: "{{ __('treatment.please_enter_at_least_10_cha') }}"
        },
      @endif
      @if($mdr)  
        mednr: {
            required: "{{ __('treatment.please_enter_MED_number') }}"
        },
      @endif
        cname: {
          required: "{{ __('treatment.please_enter_full_name') }}",
        },
        cemail: {
          required: "{{ __('treatment.please_enter_email_address') }}",
          email: "{{ __('treatment.please_enter_valid_email_address') }}"
        },
        cnumber: {
          required: "{{ __('keywords.please_enter_mobile_number') }}",
          minlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
          maxlength: "{{ __('treatment.please_enter_at_least_8_cha') }}",
        }
    },
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.input-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
});

jQuery('#saveCustomer').submit(function(){
  var cou = jQuery('select[name=ccountry] option:selected').text();
  jQuery('input[name=cnumber]').rules('remove','minlength');
  jQuery('input[name=cnumber]').rules('remove','maxlength');
    if(cou == 'Denmark'){
      jQuery('input[name=cnumber]').rules('add',{
        minlength:8,
        maxlength:8
      });
    }
});

function suggestUser(obj){
  var html = '';
  var search = jQuery(obj).val();
  var time = jQuery(obj).attr('data-time');
  time = time.replace(':','');
  var dateId = jQuery(obj).attr('date-id');
  var ulID = 'name'+time+dateId;
  jQuery("ul#"+ulID).html('');

  if( search.length > 2 ){
    $.ajax({
      type: 'POST',
      url: '/getUserDataAjax',
      data: {"_token":"{{ csrf_token() }}",'search':search},
      dataType: 'json',
      success: function (users) {
        if(!$.trim(users)){
            jQuery('#myModal #saveCustomer #formId').val(jQuery(obj).closest('tr').find('td.name ul').attr('id'));
            jQuery('#myModal').modal('show');
        }else{
          jQuery.each(users, function(index, value){
            var u = value["id"];
            var n = value["name"];
            var e = value["email"];
            var o = value["number"];
            var i = ulID;
              html += '<li class="list-group-item" onclick="addValues(`'+n+'`,`'+e+'`,`'+o+'`,`'+i+'`,`'+u+'`)" >'+value["name"]+'('+value["email"]+')</li>';
          });
          html += '<li class="list-group-item" data-toggle="modal" data-target="#myModal">{{ __('treatment.add_new') }}</li>';
          jQuery("ul#"+ulID).html(html);
        }
      },  
    });
  }
}

function addValues(name,email,number,id,userId){
  jQuery('#'+id).html('');
  var tr = jQuery('#'+id).closest('tr');
  jQuery(tr).find('td').each(function(){

    if(jQuery(this).hasClass('status')){
      var form = jQuery(this).find('form');
      if(jQuery(form).find('input[name=user_id]').val())
        jQuery(form).find('input[name=user_id]').val(userId);
      else
        jQuery(form).append("<input type='hidden' name='user_id' value='"+userId+"'>");
    }

    if(jQuery(this).hasClass('name')){
        jQuery(this).find('input').val(name);
    }
    if(jQuery(this).hasClass('email')){
        jQuery(this).find('input').val(email);
    }
    if(jQuery(this).hasClass('number')){
        jQuery(this).find('input').val(number);
    }
  });
}

function validate_cpr_number(data) {

  if(data === undefined)
    var cpr = jQuery("#cprNum").val();
  else
    var cpr = data;

  if(cpr != '' && $.isNumeric(cpr) && cpr.length == 10){

    var d = cpr.substring(0, 2);
    var m = cpr.substring(2, 4);
    var y = cpr.substring(4, 6);
    
    var fullDate = d+'/'+m+'/'+y;

    if(!isDate(fullDate)){
      if( data === undefined){
        alert("{{ __('treatment.please_enter_valid_number') }}");
        return false;
      }
      else{
        return false;
      }
    }

    if( data === undefined){

      var formID = jQuery("#field_name").val();

      jQuery('#myCPRModal').modal('hide');
      jQuery("#"+formID).append('<input type="hidden" class="insurance_cpr" name="insurance_cpr" value="'+cpr+'">');
      jQuery("#"+formID).closest('tr').find('.bookDel').find(' button[type=submit]').trigger('click');    
    }else{
      return true;
    }
  }else if(data === undefined){
    alert("{{ __('treatment.please_enter_valid_number') }}");
    return false; 
  }else{
    return false; 
  } 

}

function isDate(value) {
var re = /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
var flag = re.test(value);
return flag;
}


@can('Date Past Book')
function getCPRforInsuranceTreatment(FormID,obj){

//---- check if field already exist ----//
if(jQuery("#"+FormID).find('.insurance_cpr').length > 0){
  if(jQuery("#"+FormID).find('.insurance_cpr').val()){
    doBooking(obj);
    return false;
  }
  else{
    jQuery("#field_name").val(FormID);
    jQuery("#myCPRModal").modal('show');
    jQuery("#"+FormID).find('.insurance_cpr').remove();
  }
}

var token = $('meta[name="csrf-token"]').attr('content');
$.ajax({
    type: 'POST',
    url: '/checkCPForInsurance',
    data: jQuery("#"+FormID).serialize()+"&_token="+token,
    dataType: 'json',
    success: function (data) {
      console.log(data);
      if(data != 0){
        console.log("Field value added");
        jQuery("#"+FormID).append('<input type="hidden" class="insurance_cpr" name="insurance_cpr" value="'+data+'">');
        doBooking(obj);
      }else{
        jQuery("#field_name").val(FormID);
        jQuery("#myCPRModal").modal('show');
      }
    }
});  
}

function bookSlot(obj){

//--------- Getting data from button to get form id -----
var rtime = jQuery(obj).attr('data-time');
time = rtime.replace(':','');
var dateId = jQuery(obj).attr('date-id');
var FormID = 'form'+time+dateId;

var isInsurance = jQuery(obj).closest('tr').find('select[name=treatment] :selected').attr('data-insurance');
if(isInsurance == 1){
  getCPRforInsuranceTreatment(FormID,obj);
}else{
  doBooking(obj);
}
}

function doBooking(obj){
//--------- Getting data from button to get form id -----
var rtime = jQuery(obj).attr('data-time');
time = rtime.replace(':','');
var dateId = jQuery(obj).attr('date-id');
var FormID = 'form'+time+dateId;
var comment = treatment = department = '';

//----- Show loader --------
jQuery(obj).closest('.card-body').prepend('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">{{ __('treatment.loading') }}..</div></div>');
//--------- For notification -----
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

//--------- Getting comment AND treatment AND department of current data -----
var tr = jQuery(obj).closest('tr');
jQuery(tr).find('td').each(function(){
    if(jQuery(this).hasClass('comment')){
      comment =  jQuery(this).find('input').val();
    }
    if(jQuery(this).hasClass('treatment')){
      treatment =  jQuery(this).find('select').val();
    }
    if(jQuery(this).hasClass('department')){
      department =  jQuery(this).find('select').val();
    }
});
//----------- CSRF token ------------
var token = $('meta[name="csrf-token"]').attr('content');

//--------- submit fom with data now -----
$.ajax({
    type: 'POST',
    url: '/BookTimeSlot',
    data: jQuery("#"+FormID).serialize()+"&treatment="+treatment+"&department="+department+"&comment="+comment+"&_token="+token,
    dataType: 'json',
    success: function (data) {

      if(data['status'] == 'success'){

        var mobilePayBtn = '';
        if({{ $mobilePay }} && data['price'] > 0)
          mobilePayBtn = '<a href="javascript:;" data-slot-id="'+data['slot_id']+'" class="btn btn-sm btn-default btn-block mt-1" onclick="sendMobilePayReq(this)"><img src="images/mobilepay.svg" width="100"></a>';

        //-------- Now updating slots ----
        var btn = '<a  href="/journal/'+data['userHash']+'" data-user-id="'+data['userHash']+'" class="btn btn-default btn-sm"><i class="fas fa-circle" style="color:green;"></i>&nbsp; {{ __('treatment.start_consultation') }}</a>'+mobilePayBtn ;

        if(data['bookingCount'] == 0){
          btn = '<a  href="/journal/'+data['userHash']+'" data-user-id="'+data['userHash']+'" class="btn btn-warning btn-sm"><i class="fas fa-circle" style="color:green;"></i>&nbsp; {{ __('treatment.start_consultation') }} <br>({{ __('treatment.new_customer') }})</a>'+mobilePayBtn ;
        }

        //------- Cards list for this user
        var cards = '';
        if(data['cards'].length > 0){
          jQuery(data['cards']).each(function(index, value){

            //----- Check if date is expired -----
            var date = new Date(value['expiry_date']);
            var today = new Date();
            var diff = new Date(date.toDateString()) <= new Date(today.toDateString());

            if(diff)
              cards += '<option value="'+value['id']+'" disabled>'+value['name']+' ({{ __('treatment.expired') }})</option>';
            else
              cards += '<option value="'+value['id']+'">'+value['name']+'</option>';
          });
        }
        

        jQuery(tr).find('td').each(function(){
            
            if(jQuery(this).hasClass('status')){
              jQuery(this).removeClass('bg-success').addClass('bg-info');
              jQuery(this).find('span').html(btn);
            }
            if(jQuery(this).hasClass('pauseUnpause')){
              jQuery(this).html('');
            }
            
            if(jQuery(this).hasClass('cutCard')){
              jQuery(this).attr('data-id',data['slot_id']);
              jQuery(this).attr('data-treatment-id',treatment);
            }
            if(jQuery(this).hasClass('clipCard')){
              if(cards != ''){
                jQuery(this).html('<select name="card" class="form-control select2" data-time="'+rtime+'" onchange="cardSelect(this)"><option value="">-- {{ __('treatment.select_card') }} --</option>'+cards+'</select> ');
              }else{
                jQuery(this).html('<select name="card" class="form-control select2"><option value="">{{ __('card.no_cards') }}</option></select> ');
              }
            }
            if(jQuery(this).hasClass('number')){
              jQuery(this).html("<a href='Tel:"+data['number']+"'>"+data['number']+"</a>");
            }
            if(jQuery(this).hasClass('name')){
              jQuery(this).html(data['name']);
            }
            if(jQuery(this).hasClass('email')){
              jQuery(this).html(data['email']);
            }
            if(jQuery(this).hasClass('treatment')){
              jQuery(this).html(data['treatment']);
            }
            if(jQuery(this).hasClass('department')){
              jQuery(this).html(data['department']);
            }
            if(jQuery(this).hasClass('comment')){
              jQuery(this).html(comment);
            }
            if(jQuery(this).hasClass('bookTime')){
              jQuery(this).text(data['bookedTime']);
            }
            if(jQuery(this).hasClass('bookDel')){
              jQuery(this).html('<button type="submit" class="btn btn-danger" data-time="'+rtime+'" data-id="'+data['slot_id']+'" onclick="deleteBooking(this)">{{ __('treatment.delete_booking') }}</button>');
            }
        });
        //--------- Delete next slots -----
        for(var i=1; i < data['slots']; i++ ){
          jQuery(tr).next('tr').remove();
        }

        jQuery('.select2').select2({
          theme: 'bootstrap4'
        });

        Toast.fire({
            icon: 'success',
            title: ' {{ __('treatment.shbbs') }}'
        });

      }
      else if(data['status'] == 'exist'){
        Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.nesaaftt') }}'
        })
      }
      else if(data['status'] == 'exceeded'){
        Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.btepcsbt') }}'
        })
      }
      else{
        Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.tiaue') }}'
        })
      }
      //------ Remove loader ------
      jQuery(tr).closest('.card-body').find('.overlay').remove();  
    },
    error: function (data) {
      //------ Remove loader ------
      jQuery(tr).closest('.card-body').find('.overlay').remove();    

      Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.pmsafafatta') }}'
        });
    }
}); 
}
@endcan

@can('Date Booking Delete')
//------------- Delete booking ---------
function deleteBooking(obj){

  //----- Show loader --------
  jQuery(obj).closest('.card-body').prepend('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">{{ __('treatment.loading') }}...</div></div>');

  //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

  var tr = jQuery(obj).closest('tr');
  var slotTime = jQuery(obj).attr('data-time');
  var slotId = jQuery(obj).attr('data-id');
 
  //----------- CSRF token ------------
  var token = $('meta[name="csrf-token"]').attr('content');

  $.ajax({
      type: 'POST',
      url: '/DeleteBooking',
      data: {"_token":token,"id":slotId},
      dataType: 'json',
      success: function (data) {
        console.log(data);
        if(data['status'] == 'success'){

          //----------- Change current slot html ------
          var options = getOptions(data['treatments']);
          var department = getDepartments(data['departments']);

          ntime = slotTime.replace(':','');
          var closeTime;
          @if(Auth::user()->role == 'Super Admin')
            closeTime= '<button type="submit" class="btn btn-warning" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onclick="timePause(this)">{{ __('treatment.close_time') }}</button>';
          @endif

          jQuery(tr).find('td').each(function(){
              if(jQuery(this).hasClass('status')){
                jQuery(this).removeClass('bg-info').addClass('bg-success');
                jQuery(this).find('span').text('Available');
                jQuery(this).find('input[name=user_id]').val('');
              }
              if(jQuery(this).hasClass('number')){
                jQuery(this).html('<input type="text" class="form-control" name="number" placeholder="{{ __('keywords.number') }}" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onkeyup="suggestUser(this)" autocomplete="off">');
              }
              if(jQuery(this).hasClass('name')){
                jQuery(this).html('<input type="text" class="form-control" name="name" placeholder="{{ __('keywords.name') }}" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onkeyup="suggestUser(this)" autocomplete="off"><ul class="list-group p-a" id="name'+ntime+data['date_id']+'"></ul>');
              }
              if(jQuery(this).hasClass('email')){
                jQuery(this).html('<input type="email" class="form-control" name="email" placeholder="{{ __('keywords.email') }}" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onkeyup="suggestUser(this)" autocomplete="off">');
              }
              if(jQuery(this).hasClass('treatment')){
                jQuery(this).html('<select name="treatment" class="form-control select2" data-time="'+slotTime+'">'+options+'</select>');
              }
              if(jQuery(this).hasClass('department')){
                jQuery(this).html('<select name="department" class="form-control select2" data-time="'+slotTime+'">'+department+'</select>');
              }
              if(jQuery(this).hasClass('comment')){
                jQuery(this).html('<input type="text" class="form-control" name="comment" placeholder="{{ __('treatment.comment') }}" data-time="'+slotTime+'">');
              }
              if(jQuery(this).hasClass('bookTime') || jQuery(this).hasClass('clipCard') || jQuery(this).hasClass('cutBack') || jQuery(this).hasClass('cutCard')){
                jQuery(this).html('');
              }
              if(jQuery(this).hasClass('bookDel')){
                jQuery(this).html('<button type="submit" class="btn btn-info" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onclick="bookSlot(this)">{{ __('treatment.book') }}</button>');
              }
              if(jQuery(this).hasClass('pauseUnpause')){
                jQuery(this).html(closeTime);
              }
              
            });

  
          //--------- Create next slots -----
          for(var i=data['nextSlots']; i > 0; i-- ){
           var time = data['childSlotsTime'][i];
           var html = getTrHtml(time,data['date_id'],data['treatments'],data['departments']);
            jQuery(tr).after(html);
          }

          //-------- Updating current card balance in all slots where it used ------//
          if(data['cardID'])
            updateCardBalance(data['cardID'],data['balance']);

          jQuery('.select2').select2({
            theme: 'bootstrap4'
          });


          Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.bhbds') }}'
          });
          
        }
        else if(data['status'] == 'exceeded'){
          Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.bdthbp') }}'
          })
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.bcnbd') }}'
          })
        }

        //------ Remove loader ------
        jQuery(tr).closest('.card-body').find('.overlay').remove();
      },
      error: function (data) {
        //------ Remove loader ------
        jQuery(tr).closest('.card-body').find('.overlay').remove(); 

        Toast.fire({
              icon: 'error',
              title: '{{ __('treatment.tiauetdb') }}'
          });
         
      }
  }); 
}
@endcan

@can('Date Time Close')
function timePause(obj){
   //----- Show loader --------
   jQuery(obj).closest('.card-body').prepend('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">{{ __('treatment.loading') }}...</div></div>');
  //--------- For notification -----
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
  });

  var tr = jQuery(obj).closest('tr');
  var slotTime = jQuery(obj).attr('data-time');
  var dateID = jQuery(obj).attr('date-id');

  //----------- CSRF token ------------
  var token = $('meta[name="csrf-token"]').attr('content');

$.ajax({
    type: 'POST',
    url: '/AddBreak',
    data: {"_token":token,"dateID":dateID,'time':slotTime},
    dataType: 'json',
    success: function (data) {
      console.log(data);
      if(data['status'] == 'success'){

        //----------- Change current slot html ------        
        jQuery(tr).find('td').each(function(){
            if(jQuery(this).hasClass('status')){
              jQuery(this).removeClass('bg-success').addClass('bg-warning');
              jQuery(this).find('span').text('Break');
            }
            if(jQuery(this).hasClass('number')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('name')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('email')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('treatment')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('department')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('comment')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('bookDel')){
              jQuery(this).html('');
            }
            if(jQuery(this).hasClass('pauseUnpause')){
              jQuery(this).html('<button type="submit" class="btn btn-info" data-time="'+slotTime+'" data-id="'+data['slot_id']+'" onclick="deletePause(this)">{{ __('treatment.delete_pause') }}</button>');
            }
            
          });

          Toast.fire({
            icon: 'success',
            title: ' {{ __('treatment.shbrfbs') }}'
          });
      }
      else{
        Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.tiaetrs') }}'
        })
      }
      //------ Remove loader ------
      jQuery(tr).closest('.card-body').find('.overlay').remove();
    },
    error: function (data) {
      //------ Remove loader ------
      jQuery(tr).closest('.card-body').find('.overlay').remove();  

      Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.tiaetrs') }}'
        });
    }
  }); 
}
@endcan

@can('Date Time Open')
function deletePause(obj){
  //----- Show loader --------
  jQuery(obj).closest('.card-body').prepend('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">{{ __('treatment.loading') }}...</div></div>');
 //--------- For notification -----
 const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

  var tr = jQuery(obj).closest('tr');
  var slotTime = jQuery(obj).attr('data-time');
  var slotId = jQuery(obj).attr('data-id');
 
  //----------- CSRF token ------------
  var token = $('meta[name="csrf-token"]').attr('content');

  $.ajax({
      type: 'POST',
      url: '/DeleteBooking',
      data: {"_token":token,"id":slotId},
      dataType: 'json',
      success: function (data) {
        //console.log(data);
        if(data['status'] == 'success'){

          //----------- Change current slot html ------
          var options = getOptions(data['treatments']);
          var department = getDepartments(data['departments']);

          ntime = slotTime.replace(':','');
          
          jQuery(tr).find('td').each(function(){
              if(jQuery(this).hasClass('status')){
                jQuery(this).removeClass('bg-warning').addClass('bg-success');
                jQuery(this).find('span').text('Available');
                jQuery(this).find('input[name=user_id]').val('');
              }
              if(jQuery(this).hasClass('number')){
                jQuery(this).html('<input type="text" class="form-control" name="number" placeholder="{{ __('keywords.number') }}" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onkeyup="suggestUser(this)" autocomplete="off">');
              }
              if(jQuery(this).hasClass('name')){
                jQuery(this).html('<input type="text" class="form-control" name="name" placeholder="{{ __('keywords.name') }}" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onkeyup="suggestUser(this)" autocomplete="off"><ul class="list-group p-a" id="name'+ntime+data['date_id']+'"></ul>');
              }
              if(jQuery(this).hasClass('email')){
                jQuery(this).html('<input type="email" class="form-control" name="email" placeholder="{{ __('keywords.email') }}" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onkeyup="suggestUser(this)" autocomplete="off">');
              }
              if(jQuery(this).hasClass('treatment')){
                jQuery(this).html('<select name="treatment" class="form-control select2" data-time="'+slotTime+'">'+options+'</select>');
              }
              if(jQuery(this).hasClass('department')){
                jQuery(this).html('<select name="department" class="form-control select2" data-time="'+slotTime+'">'+department+'</select>');
              }
              if(jQuery(this).hasClass('comment')){
                jQuery(this).html('<input type="text" class="form-control" name="comment" placeholder="{{ __('treatment.comment') }}" data-time="'+slotTime+'">');
              }
              if(jQuery(this).hasClass('bookTime')){
                jQuery(this).html('');
              }
              if(jQuery(this).hasClass('bookDel')){
                jQuery(this).html('<button type="submit" class="btn btn-info" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onclick="bookSlot(this)">{{ __('treatment.book') }}</button>');
              }
              if(jQuery(this).hasClass('pauseUnpause')){
                jQuery(this).html('<button type="submit" class="btn btn-warning" data-time="'+slotTime+'" date-id="'+data['date_id']+'" onclick="timePause(this)">{{ __('treatment.close_time') }}</button>');
              }
              
            });

  
          jQuery('.select2').select2({
            theme: 'bootstrap4'
          });
          
          Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.brhbds') }}'
          });
        }
        else{
          Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.brcnbd') }}'
          })
        }
        //------ Remove loader ------
        jQuery(tr).closest('.card-body').find('.overlay').remove();  
      },
      error: function (data) {
        
        //------ Remove loader ------
        jQuery(tr).closest('.card-body').find('.overlay').remove(); 

        Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.tiauetdbr') }}'
        });
   
      }
  }); 

}
@endcan

function getTrHtml(time,dateID,treatments,departments){
  
  var ntime = time.replace(':','');
  var options = getOptions(treatments);
  var departments = getDepartments(departments);
  
  var closeTime = '';

  @if(Auth::user()->role == 'Super Admin')
    closeTime= '<button type="submit" class="btn btn-warning" data-time="'+time+'" date-id="'+dateID+'" onclick="timePause(this)">{{ __('treatment.close_time') }}</button>';
  @endif

  var tr = '<tr>';
  tr += '<td class="bg-success text-center status"><form id="form'+ntime+dateID+'"><input type="hidden" name="date_id" value="'+dateID+'" /><input type="hidden" name="data_time" value="'+time+'" /></form><span> {{ __('treatment.available') }}</span></td><td class="text-center time">'+time+'</td><td class="name"><input type="text" class="form-control" name="name" placeholder="{{ __('keywords.name') }}" data-time="'+time+'" date-id="'+dateID+'" onkeyup="suggestUser(this)" autocomplete="off" /><ul class="list-group p-a" id="name'+ntime+dateID+'"></ul></td><td class="number"><input type="text" class="form-control" name="number" placeholder="{{ __('keywords.number') }}" data-time="'+time+'" date-id="'+dateID+'" onkeyup="suggestUser(this)" autocomplete="off" /></td><td class="email"><input type="email" class="form-control" name="email" placeholder="{{ __('keywords.email') }}" data-time="'+time+'" date-id="'+dateID+'" onkeyup="suggestUser(this)" autocomplete="off" /></td><td class="text-center clipCard"></td><td class="text-center cutBack"></td><td class="text-center cutCard"></td><td class="department"><select name="department" class="form-control select2 " data-time="'+time+'" >'+departments+'</select></td><td class="treatment"><select name="treatment" class="form-control select2 " data-time="'+time+'" >'+options+'</select></td><td class="text-center bookTime"></td><td class="comment"><input type="text" class="form-control" name="comment" placeholder="{{ __('treatment.comment') }}" data-time="'+time+'" /></td><td class="text-center bookDel"><button type="submit" class="btn btn-info" data-time="'+time+'" date-id="'+dateID+'" onclick="bookSlot(this)">{{ __('treatment.book') }}</button></td><td class="text-center pauseUnpause">'+closeTime+'</td>';
  tr += '</tr>';
s

  return tr;
}


function getOptions($treatments){
    var options;
    jQuery($treatments).each(function(index, value){
    @if($CPRForInsurance == 0 )
      if(value['is_insurance'] == 0){
        var time = '';
        if(value['time_shown'])
          time = value['time_shown'];
        else
          time = value['inter'];
        options += '<option value="'+value['id']+'" data-insurance="'+value['is_insurance']+'">'+value['treatment_name']+' ('+time+' min)</option> ';
      }
    @else
      var time = '';
        if(value['time_shown'])
          time = value['time_shown'];
        else
          time = value['inter'];
        options += '<option value="'+value['id']+'" data-insurance="'+value['is_insurance']+'">'+value['treatment_name']+' ('+time+' min)</option> ';
    @endif    
    });
    return options;
}


function getDepartments($departments){
    var options;
    jQuery($departments).each(function(index, value){
      options += '<option value="'+value['id']+'">'+value['name']+'</option> ';
    });
    return options;
}


</script>


<script>
    jQuery(document).ready(function(){
      //----------- CSRF token ------------
      var token = $('meta[name="csrf-token"]').attr('content');
      var oldTableData;
      var oldTableData;

      $.ajax({
          type: "POST",
          data: { 'dates':dates,'_token':token},
          url: "/getDateData",
          success: function(response){
            oldTableData = response;
            showTable(response);
          },
      });

      setInterval(function(){ 
        $.ajax({
          type: "POST",
          data: { 'dates':dates,'_token':token},
          url: "/getDateData",
          success: function(response){
            if( !equalArray(oldTableData,response) ){
              oldTableData = response;
              showTable(response);
            }  
          }
        });
      },60000);

    });

    function equalArray(a, b) {
        return JSON.stringify(a) == JSON.stringify(b);
    }

    function showTable(data){
        jQuery.each( data, function( key, value ) {
          var id;
          jQuery.each( value, function( ke, val ) {
              id = '#tr-'+ke;
              var html = '';
              jQuery.each( val, function( k, v ) {
                html += v;
              });
              jQuery('.calenderTable').show();
              jQuery(id).html('');
              jQuery(id).html(html);
          }); 
          
        }); 
    }


  function cardSelect(obj){
    var token = $('meta[name="csrf-token"]').attr('content');
    var cardID = jQuery(obj).val();
    //----- Getting ids of slot and treatment from cutCard td ---
    var slotID = jQuery(obj).closest('tr').find('.cutCard').attr('data-id');
    var treatmentID = jQuery(obj).closest('tr').find('.cutCard').attr('data-treatment-id');

    $.ajax({
      type: "POST",
      data: { 'id':cardID,'_token':token,'slotID':slotID},
      url: "/getCardUsedClipsAjax",
      dataType: 'json',
      success: function(response){
        if(response['status'] == 'new'){
          jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
          jQuery(obj).closest('tr').find('.cutCard').html("<button data-treatment-id='"+treatmentID+"' data-slot-id='"+slotID+"' data-card-id='"+cardID+"' onclick='cutClips(this)' class='btn btn-info btn-sm'>{{ __('treatment.cut_in_clips') }}</button>");
        }
        else{
          jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
          jQuery(obj).closest('tr').find('.cutCard').html('<button class="btn btn-warning btn-sm" data-clip-id="'+response['id']+'" onclick="cutBackClips(this)">{{ __('treatment.undo_cut_in_clips') }}</button>');
        }
      }
    });
  }

  function cutClips(obj){
    //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');
    var cardID = jQuery(obj).attr('data-card-id');
    var treatmentID = jQuery(obj).attr('data-treatment-id');
    var slotID = jQuery(obj).attr('data-slot-id');

    $.ajax({
      type: "POST",
      data: { 'cardID':cardID,'_token':token,'treatmentID':treatmentID,'slotID':slotID},
      url: "/bookClipsAjax",
      dataType: 'json',
      success: function(response){
          console.log(response);
          if(response['status'] == 'success'){
            Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.clips_used_successfully') }}'
            });

            //-------- Updating current card balance in all slots where it used ------//
            updateCardBalance(cardID,response['balance']);

            jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
            jQuery(obj).closest('tr').find('.cutCard').html('<button class="btn btn-warning btn-sm" data-clip-id="'+response['id']+'" onclick="cutBackClips(this)">{{ __('treatment.undo_cut_in_clips') }}</button>');
          }
          else if(response['status'] == 'less'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.not_enough_clips_available') }}'
            })
          }
          else if(response['status'] == 'exist'){
            Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.card_already_used_for_this_booking') }}'
            })
          }
          else{
            Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.tiaetuc') }}'
            })
          }
      }
    });
  }

  function cutBackClips(obj){
    //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    var token = $('meta[name="csrf-token"]').attr('content');
    id = jQuery(obj).attr('data-clip-id');  

    $.ajax({
      type: "POST",
      data: { 'id':id,'_token':token},
      url: "/deleteClipAjax",
      dataType: 'json',
      success: function(response){
          console.log(response);
          if(response['status'] == 'success'){
            Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.clips_deleted_successfully') }}'
            });

            //-------- Updating current card balance in all slots where it used ------//
            updateCardBalance(response['card'],response['balance']);

            jQuery(obj).closest('tr').find('.cutBack').html(response['balance']);
            jQuery(obj).closest('tr').find('.cutCard').html("<button data-treatment-id='"+response['treatment']+"' data-slot-id='"+response['slot']+"' data-card-id='"+response['card']+"' onclick='cutClips(this)' class='btn btn-info btn-sm'>{{ __('treatment.cut_in_clips') }}</button>");
          }
          else{
            Toast.fire({
              icon: 'error',
              title: ' {{ __('treatment.tiaetdc') }}'
            })
          }
      }
    });
  }
    
  function updateCardBalance(cardID,balance){
    jQuery('body').find('.clipCard').each(function(){
      var cardId = jQuery(this).find('select').val();
      if( cardId != '' || cardId != 'undefined' ){
        if(cardID == cardId){
            jQuery(this).next().html(balance);
        }
      }
    });
  } 

  function sendMobilePayReq(obj){
 
    var slotID = jQuery(obj).attr('data-slot-id');

    //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    $.ajax({
      type: "POST",
      data: { 'id':slotID,'_token':'{{ csrf_token() }}'},
      url: "/sendMobilePaySms",
      success: function(response){
        
        if(response == 1){
            Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.mobile_pay_sms') }}'
            });

          jQuery(obj).html('<i class="fa fa-check magin-color-icon"></i><img src="images/mobilepay.svg" width="100">');
        }
        else{
          Toast.fire({
              icon: 'success',
              title: ' {{ __('treatment.tiauetud') }}'
            });
        }
          
      }
    });
  } 


  @can('Date Book')
  function getAvailableTreatments(obj){
    var bookingId = jQuery(obj).attr('data-id');
    $.ajax({
      type: "POST",
      data: { 'id':bookingId,'_token':'{{ csrf_token() }}'},
      url: "/getAvailableTreatments",
      success: function(response){
        var data = "<select class='form-control' data-id='"+bookingId+"' name='new-treatment' id='new-treatment' onchange='updateTreatment(this)'>"+response+"</select>";
        jQuery(data).insertAfter(obj);
        jQuery(obj).remove();
      }
    });
  }

  function updateTreatment(obj){
    
    //--------- For notification -----
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000
    });

    //----- Show loader --------
    jQuery(obj).closest('.card-body').prepend('<div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">{{ __('treatment.loading') }}..</div></div>');

    var bookingId = jQuery(obj).attr('data-id');
    var newTreatmentId = jQuery(obj).val();
    var newTreatmentTxt = jQuery(obj).find("option:selected").text();

    //------ update treatment for this booking -----//
    $.ajax({
      type: "POST",
      data: { 'id':bookingId,'treatment':newTreatmentId,'_token':'{{ csrf_token() }}'},
      url: "/updateTreatmentId",
      success: function(response){

        var data = JSON.parse(response);

        if(data['status'] == 'delete'){
          //---- update
          jQuery('<a href="javascript:;" title="{{ __('keywords.click_to_change') }}" data-id="'+bookingId+'" onclick="getAvailableTreatments(this)">'+newTreatmentTxt+"</a>").insertAfter(obj);

          //--- add new slots
          var thisTr = jQuery(obj).closest('tr');
          for(var i=0; i<data['count']; i++){
            var html = getTrHtml(data['times'][i],data['date_id'],data['treatments'],data['departments']);
            jQuery(html).insertAfter(thisTr);
          }

          //------ Remove loader ------
          jQuery(obj).closest('.card-body').find('.overlay').remove(); 

          //--- delete select
          jQuery(obj).remove();

        }
        else if(data['status'] == 'add')
        {
          //---- update
          jQuery('<a href="javascript:;" title="{{ __('keywords.click_to_change') }}" data-id="'+bookingId+'" onclick="getAvailableTreatments(this)">'+newTreatmentTxt+"</a>").insertAfter(obj);

          //---- delete ext slots
          var thisTr = jQuery(obj).closest('tr');
          for(var i=0; i< data['count']; i++){
            jQuery(thisTr).next('tr').remove();
          }

          //------ Remove loader ------
          jQuery(obj).closest('.card-body').find('.overlay').remove(); 

          //--- delete select
          jQuery(obj).remove();
        }
        else if(data['status'] == 'update'){
          //---- update
          jQuery('<a href="javascript:;" title="{{ __('keywords.click_to_change') }}" data-id="'+bookingId+'" onclick="getAvailableTreatments(this)">'+newTreatmentTxt+"</a>").insertAfter(obj);
          
          //------ Remove loader ------
          jQuery(obj).closest('.card-body').find('.overlay').remove(); 
          
          //--- delete select
          jQuery(obj).remove();
        }
        else{
          //------ Remove loader ------
          jQuery(obj).closest('.card-body').find('.overlay').remove(); 

          Toast.fire({
            icon: 'error',
            title: ' {{ __('treatment.nesaaftt') }}'
          })
        }
      }
    });
  }
  @endcan
</script>
@stop