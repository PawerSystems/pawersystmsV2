@if(auth()->user()->role != 'Customer')
<footer class="main-footer">
  <strong>{{ __('keywords.copyright') }} &copy; {{ config('app.name', 'Pawersystems') }} - {{ date('Y') }}</strong>
  <!-- All rights reserved. -->
  <div class="float-right d-none d-sm-inline-block">
    <b>{{ __('keywords.version') }}</b> 2.0
  </div>
</footer>
@else
<link href="{{asset('web/style.min.css')}}" rel="stylesheet">
<style>
    .container-fluid > .row{ margin:0; }
</style>
<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-12">   
                <span class="copyright">{{ __('web.question_please_contact') }} <a href="mailto:{{ $email }}">{{ $business}}</a></span>
            </div> 
            <div class="col-md-12">
                <ul class="list-inline quicklinks">
                    <li class="list-inline-item">
                    <a href="{{ Route('gdpr',session('business_name')) }}">GDPR</a>
                    </li>
                    <!-- <li class="list-inline-item">
                    <a href="{{ Route('resendemail',session('business_name')) }}"> {{ __('web.resendEmail') }}</a>
                    </li> -->
                </ul>
                <span class="copyright">{{ __('keywords.copyright') }} &copy; {{ config('app.domain') }}</span>

            </div>
        </div>
    </div>
</footer>
@endif
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap -->
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE -->
<script src="{{asset('dist/js/adminlte.js')}}"></script>
<script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<!-- SweetAlert2 -->
<script src="{{asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{asset('plugins/toastr/toastr.min.js') }}"></script>

<!-- Bootstrap Switch -->
<script src="{{asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>

<!-- jquery-validation -->
<script src="{{asset('plugins/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{asset('plugins/jquery-validation/additional-methods.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>

<!-- Select2 -->
<script src="{{asset('plugins/select2/js/select2.full.min.js') }}"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="{{asset('plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
<!-- InputMask -->
<script src="{{asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{asset('plugins/moment/moment-with-locales.js') }}"></script>

<script src="{{asset('plugins/inputmask/min/jquery.inputmask.bundle.min.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>


<!-- OPTIONAL SCRIPTS -->
@if( \Request::is('dashboard') || \Request::is('stats') )
<script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('dist/js/pages/dashboard.js')}}"></script>

<script src="{{asset('plugins/flot/jquery.flot.js')}}"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="{{asset('plugins/flot-old/jquery.flot.resize.min.js')}}"></script>
<!-- FLOT PIE PLUGIN - also used to draw donut charts -->
<script src="{{asset('plugins/flot-old/jquery.flot.pie.min.js')}}"></script>
<script>

  function labelFormatter(label, series) {
    return '<div style="font-size:16px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
      + label
      + '<br>'
      + Math.round(series.percent) + '%</div>'
  }
</script>
@endif
<script src="{{asset('plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- fullCalendar 2.2.5 -->
<script src="{{asset('plugins/fullcalendar/dep/popper.js')}}"></script>
<script src="{{asset('plugins/fullcalendar/dep/tooltip.js')}}"></script>
<script src="{{asset('plugins/fullcalendargrid/lib/main.js')}}"></script>

<!--- Duble Scroll around tables ----->
<script src="{{asset('plugins/double-scroll/index.js')}}"></script>

<script type="text/javascript">
jQuery(document).ready(function () {
if( jQuery('.custom-file-input').length > 0 )
  bsCustomFileInput.init();
});

jQuery(function () {
    jQuery('.select2').select2({
      theme: 'bootstrap4'
    });
});

</script>
<script>
jQuery(function () {
  //---- For data tables ---------
  if( jQuery('#datatable').length > 0 ){

    @foreach ( Config::get('languages') as $key => $val )
      @if(Lang::locale() == $key)
        var lang = '{{ $val['display'] }}';
      @endif
    @endforeach

    jQuery('#datatable').DataTable({
      language: {
          url: '//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/'+lang+'.json'
      },
      "paging": true,
      "lengthChange": true,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
      "aaSorting": [],
    });
  }  
});

jQuery('#userSwitchForm').one('submit', function(e) {
  e.preventDefault();
  var admin = jQuery('select#domain option:selected').attr('data-admin');
  jQuery('#superadmin').val(admin);
  jQuery(this).submit();
});

jQuery(document).ready(function(){

  @php
      $languages = Config::get('languages');
      $key = $languages[Lang::locale()]['lang-variable'];
      $jsFormates = include(storage_path("/date-format/datetimepicker.php"));
      $dateFormat = App\Models\Business::find(auth()->user()->business_id)->Settings->where('key','date_format')->first();
      $dateFormatValue = ( $dateFormat ? $dateFormat->value : 'Y-M-d' );
  @endphp

  //---------------------------------//
  var dateToday = new Date(); 
  
  jQuery('#date').datetimepicker({
    format:'{{ $jsFormates[$dateFormatValue] }}',
    minDate: dateToday,
    locale: '{{$key}}',
    ignoreReadonly: true,
  });  
  jQuery("#date").on("change.datetimepicker",function (e) {
    var formatedValue = e.date.format('YYYY-MM-DD');
    jQuery("#_date").attr('value',formatedValue);
  });

  jQuery('#date_nolimit').datetimepicker({
    format:'{{ $jsFormates[$dateFormatValue] }}',
    locale: '{{$key}}',
    ignoreReadonly: true,
  });  
  jQuery("#date_nolimit").on("change.datetimepicker",function (e) {
    var formatedValue = e.date.format('YYYY-MM-DD');
    jQuery("#_date").attr('value',formatedValue);
  });
  //---------------------------------//

  jQuery('.date').datetimepicker({
    format:'{{ $jsFormates[$dateFormatValue] }}',
    locale: '{{$key}}',
    ignoreReadonly: true,
  });
  jQuery(".date").on("change.datetimepicker",function (e) {
    var formatedValue = e.date.format('YYYY-MM-DD');
    var id = jQuery(this).attr('id');
    jQuery("#_"+id).attr('value',formatedValue);
  });
  //---------------------------------//

  jQuery('.reservationtime').datetimepicker({
    format:'{{ $jsFormates[$dateFormatValue] }}',
    locale: '{{$key}}',
    ignoreReadonly: true,
  });
  jQuery(".reservationtime").on("change.datetimepicker",function (e) {
    var formatedValue = e.date.format('YYYY-MM-DD');
    var id = jQuery(this).attr('id');
    jQuery("#_"+id).attr('value',formatedValue);
  });
  //---------------------------------//

  jQuery('#reservationdate').datetimepicker({
      // format: 'DD-MM-YYYY HH:mm:ss',
      format:'{{ $jsFormates[$dateFormatValue] }} HH:mm:ss',
      locale: '{{$key}}',
      useCurrent: false,
      showTodayButton: true,
      showClear: true,
      toolbarPlacement: 'bottom',
      sideBySide: true,
      ignoreReadonly: true,
      icons: {
          time: "fa fa-clock-o",
          date: "fa fa-calendar",
          up: "fa fa-arrow-up",
          down: "fa fa-arrow-down",
          previous: "fa fa-chevron-left",
          next: "fa fa-chevron-right",
          today: "fa fa-clock-o",
          clear: "fa fa-trash-o"
      }
  });
  jQuery("#reservationdate").on("change.datetimepicker",function (e) {
    var formatedValue = e.date.format('YYYY-MM-DD H:m:ss');
    jQuery("#reservationdate input[name=_schedule]").attr('value',formatedValue);
  });
  //---------------------------------//

  jQuery('#reservationtime').datetimepicker({
      // format: 'DD-MM-YYYY HH:mm:ss',
      format:'HH:mm',
      locale: '{{$key}}',
      useCurrent: false,
      showTodayButton: true,
      showClear: true,
      toolbarPlacement: 'bottom',
      sideBySide: true,
      icons: {
          time: "fa fa-clock-o",
          date: "fa fa-calendar",
          up: "fa fa-arrow-up",
          down: "fa fa-arrow-down",
          previous: "fa fa-chevron-left",
          next: "fa fa-chevron-right",
          today: "fa fa-clock-o",
          clear: "fa fa-trash-o"
      }
  });

});

</script> 
</body>
</html>
