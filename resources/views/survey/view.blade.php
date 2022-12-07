<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Pawersystems') }}</title>
  <link rel="icon" href="/images/apple-icon-57x57.png" type="image/icon type">

  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- Jquery Js File -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

</head>

 
<body class="hold-transition sidebar-mini sidebar-collapse layout-fixed" style="overflow-x:hidden;">
<div wrapper="wrapper">
  

<style>
    footer.main-footer{ display:none !important; }
    @font-face {
	font-family: Gilroy Regular;
	src: url('../fonts/Gilroy-Regular.WOFF') format('WOFF');
}

@font-face {
	font-family: Gilroy SemiBold;
	src: url('../fonts/Gilroy-SemiBold.WOFF') format('WOFF');
}

@font-face {
	font-family: Gilroy Bold;
	src: url('../fonts/Gilroy-Bold.WOFF') format('WOFF');
}

@font-face {
	font-family: Gilroy Medium;
	src: url('../fonts/Gilroy-Medium.WOFF') format('WOFF');
}

body {
	font-family: 'Gilroy Regular', sans-serif;
}

ul,
li {
	list-style: none;
	padding: 0;
	margin: 0;
}

a,
button,
input,
textarea {
	text-decoration: none !important;
	outline: none !important;
}


/* Navbar Css */

.landing-home-inner {
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	padding-top: 8vh;
}

.heading {
	margin: 30px 0;
	font-family: Gilroy SemiBold;
	color: #000;
	font-size: 60px;
}

.heading-small {
	color: #000;
	font-size: 20px;
	text-align: center;
	line-height: 30px;
	margin-bottom: 20px;
	max-width: 950px;
	margin: 0 auto;
}

.feature-inner {
	margin: 100px 0px;
}

.feature-que {
	display: flex;
	align-items: center;
	margin-bottom: 50px;
}

.num {
	width: 40px;
	height: 40px;
	background: #000;
	border-radius: 50%;
	color: #fff;
	display: flex;
	justify-content: center;
	align-items: center;
	font-size: 20px;
	font-family: Gilroy SemiBold;
	margin-right: 20px;
}

.que {
	width: 90%;
	color: #000;
	font-size: 20px;
	font-family: Gilroy SemiBold;
}

.feature-ans {
	display: flex;
	justify-content: space-between;
	align-items: center;
	flex-wrap: wrap;
}

.box-name {
	height: 50%;
	width: 100%;
	display: flex;
	justify-content: center;
	align-items: center;
	font-family: Gilroy SemiBold;
	text-align: center;
}

.feature-ans-box {
	height: 170px;
	background: #fff;
	width: 170px;
	border-radius: 6px;
	box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.2);
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	font-size: 15px;
	letter-spacing: 1px;
	padding: 10px;
	text-align: center;
	cursor: pointer;
	font-family: Gilroy SemiBold;
	transition: all 0.4s ease-in-out;
	border: 2px solid #fff;
	margin-bottom: 20px;
}

.feature-ans-box:hover {
	transition: all 0.4s ease-in-out;
	border: 2px solid #000;
	box-shadow: none;
}

.feature-ans-box.active {
	color: #56be8e;
	transition: all 0.4s ease-in-out;
	border: 2px solid #56be8e;
	box-shadow: none;
}

.cat-list {
	display: flex;
	justify-content: flex-start;
	flex-wrap: wrap;
	align-items: center;
}

.cat-image {
	position: relative;
	text-align: center;
}

.image-checkbox {
	cursor: pointer;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	border: 4px solid transparent;
	margin-bottom: 0;
	outline: 0;
	width: 100%;
}

input[type="checkbox"] {
	/*display: none;*/
}

input[type="radio"]:checked+.image-checkbox-checked {
	border: 1px solid #56be8e;
}

.checked-img {
	position: absolute;
	color: #000;
	background-color: transparent;
	top: 0;
	right: 0;
	margin-left: 150px;
	padding: 10px;
	opacity: 0;
	transition: opacity .4s ease;
}

.checked-img:last-child {
	left: 0;
	right: unset;
}

.cat-list li {}

.image-checkbox-checked .checked-img {
	opacity: 1!important;
}

.image-checkbox-checked,
.image-checkbox-checked:hover {
	border: 2px solid #56be8e;
}

.cat-detail {
	font-size: 18px;
	text-transform: capitalize;
	font-weight: bold;
	color: #2c2f35;
}

.cat-img {
	height: 50px;
	vertical-align: middle;
	display: flex;
	justify-content: center;
	align-items: center;
}

.feature-ans {
	display: flex;
	justify-content: space-between;
	flex-wrap: wrap;
	align-items: center;
}

.feature-ans li {
	width: 170px;
	height: 170px;
	box-shadow: 0px 0px 9px 0px rgba(0, 0, 0, 0.11);
	border-radius: 10px;
	cursor: pointer;
	background: #fff;
	display: flex;
	flex-direction: column;
	justify-content: space-between;
	align-items: center;
	position: relative;
	padding: 50px 20px;
	margin: 0px 0px 50px 0px;
	transition: all .6s ease;
	border: 2px solid #fff;
}

.image-checkbox {
	cursor: pointer;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	-webkit-box-sizing: border-box;
	border: 4px solid transparent;
	margin-bottom: 0;
	outline: 0;
	width: 100%;
}

[type="radio"]:checked,
[type="radio"]:not(:checked) {
	position: absolute;
	left: -9999px;
}

[type="radio"]:checked+label,
[type="radio"]:not(:checked)+label {
	position: relative;
	padding-left: 28px;
	cursor: pointer;
	line-height: 20px;
	display: inline-block;
	color: #666;
}

[type="radio"]:checked+label:before,
[type="radio"]:not(:checked)+label:before {
	content: '';
	position: absolute;
	left: 0;
	top: 0;
	width: 25px;
	height: 25px;
	border: 2px solid #d2cdcd;
	border-radius: 100%;
	background: #fff;
	transition: all .6s ease;
}

[type="radio"]:checked+label:after,
[type="radio"]:not(:checked)+label:after {
	content: '';
	width: 13px;
	height: 13px;
	transition: all .4s ease;
	background: #d2cdcd;
	position: absolute;
	top: 6px;
	left: 6px;
	border-radius: 100%;
	-webkit-transition: all 0.2s ease;
}

[type="radio"]:not(:checked)+label:after {
	opacity: 1;
	-webkit-transform: scale(1);
	transform: scale(1);
}

[type="radio"]:checked+label:after {
	opacity: 1;
	-webkit-transform: scale(1);
	transform: scale(1);
	background: #56be8e;
}

[type="radio"]:checked+label:before {
	border: 2px solid #56be8e;
}

.feature-ans li:hover label:before {
	border: 2px solid #000;
}

.feature-ans li:hover label:after {
	background: #000;
}

.feature-ans li:hover {
	border: 2px solid #000;
}

.feature-ans .image-checkbox-checked:hover {
	border: 2px solid #56be8e;
}

.image-checkbox-checked:hover label:after {
	background: #56be8e!important;
}

.image-checkbox-checked:hover label:before {
	border: 2px solid #56be8e!important;
}

.image-checkbox-checked {
	border: 2px solid #56be8e!important;
}

.comment_text,#email,#name {
	width: 100%;
	border-radius: 6px 6px 6px 6px;
	box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.2);
	color: #555555;
	border: 2px solid #fff;
	line-height: 30px;
	padding: 20px;
	transition: all 0.4s ease-in-out;
	resize: none;
	font-size: 15px;
	letter-spacing: 1px;
	font-family: Gilroy SemiBold;
}

.comment_text:focus,#email:focus,#name:focus {
	border: 2px solid #000;
	box-shadow: none;
}

.few-line {
	color: #000;
	font-size: 20px;
	font-family: Gilroy SemiBold;
	letter-spacing: 1px;
	margin: 40px 0 20px;
	line-height: 30px;
}

.wrap {
	margin: 50px auto 100px;
}

.btn-0 {
	position: relative;
	display: block;
	overflow: hidden;
	width: 100%;
	height: 60px;
	max-width: 250px;
	margin: 0 auto;
	text-transform: uppercase;
	border: 2px solid #000;
	background: #000;
	letter-spacing: 1px;
	border-radius: 30px;
	display: flex;
	justify-content: center;
	align-items: center;
	font-family: Gilroy SemiBold;
	color: #fff;
	font-size: 20px;
	transition: all 0.4s ease-in-out;
}

.btn-0:hover {
	color: #000;
	background-color: #fff;
}

@media (max-width:1200px) {
	.feature-ans {
		justify-content: center;
	}
	.feature-ans li {
		margin: 20px;
	}
	.feature-inner {
		margin: 50px 0px;
	}
}

@media (max-width:768px) {
	.heading {
		font-size: 40px;
	}
	.logo {
		width: 50%;
	}
}

@media (max-width:550px) {
	.heading {
		font-size: 30px;
	}
	.heading-small {
		font-size: 16px;
	}
	.feature-ans-box {
		margin: 10px;
	}
	.que {
		font-size: 16px;
	}
	.num {
		width: 30px;
		height: 30px;
		font-size: 16px;
	}
}


.nav-pills li, nav-pills li{
border-radius: 3px;
    color:white;
     background: #808080;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000', endColorstr='#808080');
    background: -webkit-gradient(linear, left top, left bottom, from(#000000), to(#808080));
    background: -moz-linear-gradient(top, #000000, #808080);
}
.nav-pills li a{
color: white;
    padding: 10px;
    font-weight: bold;
  
}

.nav-pills li a:hover{
border-radius: 3px;
     background: #808080;
  
}
.nav-pills li a:focus{
border-radius: 3px;
     background: #808080;
  
}


.button {
    background: #808080;
    float: right;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000', endColorstr='#808080');
    background: -webkit-gradient(linear, left top, left bottom, from(#000000), to(#808080));
    background: -moz-linear-gradient(top, #000000, #808080);
    -moz-border-radius: 3px;
    color:white;
    text-transform: uppercase;
    font-weight: bold;
}
.logo {
    text-align: center;
}
.button:focus{
      background: #808080;
    float: right;
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#000000', endColorstr='#808080');
    background: -webkit-gradient(linear, left top, left bottom, from(#000000), to(#808080));
    background: -moz-linear-gradient(top, #000000, #808080);
    -moz-border-radius: 3px;
}

.button:hover{
    background: #808080;
}



.result {
    font-size: 15px;
}
</style>

<form action="{{ Route('saveSurvey',session('business_name')) }}" method="post">
	@csrf
    <div class="landing-main">
        <div class="container">
            <div class="landing-home-inner">
                @if(!$business->logo)
                    @if($business->brand_name)
                        {{ $business->brand_name }}
                    @else
                        {{ config('app.domain') }}
                    @endif
                @else
                    <img src="/images/{{ $business->logo }}" class="img-responsive logo" width="300">    
                @endif
            @if(Session::get('success') || Session::get('errro') )
				<h1 class="heading text-center">{{ __('survey.tyfytysdhbs') }}</h1>
			</div>
			<script>
				var delay = 7000; //--- redirect after 7 sec -----
				var url = '{{ Route("booking",session("business_name")) }}'
				setTimeout(function(){ window.location = url; }, delay);
			</script>	
			@else
                
				<h1 class="heading">{{ __('survey.satisfaction_survey') }}</h1>
				<div class="heading-small">
					{{ __('survey.yoiifu') }}
				</div>
				<div class="heading-small">
					{{ __('survey.survey_gole') }}
					
				</div>
            </div>
			
            @php $count = 1; $idArr = array(); @endphp
            @foreach($questions as $question)
				@php array_push($idArr,$question->id); @endphp
                <div class="feature-inner">
                    <div class="feature-que">
                        <div class="num">
                            {{ $count }}
                        </div>
                        <div class="que">
							<input type="hidden" name="question-{{$question->id}}" value="{{$question->id}}">
                            {{ $question->title }}
                        </div>
                    </div>
                    @if($question->options->where('is_active',1)->count() > 0)
                    <div class="cat-inner">
                        <ul class="cat-list feature-ans">
                            @foreach($question->options->where('is_active',1) as $option)
                            <li>
                                <div class="box-name">
                                    {{ $option->value }}	
                                </div>
								
                                <input type="radio" id="test{{$option->id}}" name="option-{{$question->id}}" value="{{$option->id}}" >
                                <label for="test{{$option->id}}"></label>
                            </li>                            
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="feature-ans">
                        <textarea class="comment_text" rows="4" name="comment-{{$question->id}}" placeholder="{{ __('survey.tych') }} ...."></textarea>
                    </div>
                    @endif
                </div>
                @php $count++; @endphp
            @endforeach
			<input type="hidden" name="ids" value="{{ implode(',',$idArr) }}">
            <div class="feature-inner">
                <div class="feature-ans">
                    <textarea id="name" rows="1" name="name" placeholder="{{ __('keywords.name') }} ({{ __('keywords.optional') }})"></textarea>
                </div>
            </div>
            <div class="feature-inner">
                <div class="feature-ans">
                    <textarea id="email" rows="1" name="email" placeholder="{{ __('keywords.email') }} ({{ __('keywords.optional') }})"></textarea>
                </div>
            </div>
            <div class=" text-center">
				<div class="few-line">
					{{ __('survey.survey_greetings') }}
				</div>  
                <div class="few-line">
                    @if($business->brand_name)
                        {{$business->brand_name}}
                    @else
                        {{ $business->business_name }} 
                    @endif     
                </div>  
            </div>
            <div class="wrap">
                <input type="submit" class="btn-0" value="{{ __('keywords.submit') }}">   
            </div>
			@endif
        </div>
    </div>
</form> 

      <script>
         // image gallery
         // init the state from the input
         jQuery(".image-checkbox").each(function() {
             if (
                jQuery(this)
                 .find('input[type="radio"]')
                 .first()
                 .attr("checked")
             ) {
                jQuery(this).addClass("image-checkbox-checked");
             } else {
                jQuery(this).removeClass("image-checkbox-checked");
             }
         });
         
         // sync the state to the input
         jQuery(".cat-list li").click(function(e) {
         
            jQuery(this).addClass('image-checkbox-checked').siblings().removeClass('image-checkbox-checked');
             var $checkbox = jQuery(this).find('input[type="radio"]');
             $checkbox.prop("checked", !$checkbox.prop("checked"));
         
             e.preventDefault();
         });
         
      </script>
