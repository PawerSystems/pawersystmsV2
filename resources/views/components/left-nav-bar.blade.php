@php $clipT = $clipE = $department = $surveySetting = ''; @endphp
    @foreach($settings as $setting)
        @if($setting->key == 'clipboard_treatment')
            @if($setting->value == 'true')
                @php $clipT = 1; @endphp
            @else
                @php $clipT = 0 @endphp
            @endif
        
        @elseif($setting->key == 'clipboard_event')
            @if($setting->value == 'true')
                @php $clipE = 1; @endphp
            @else
                @php $clipE = 0 @endphp
            @endif    
          
        @elseif($setting->key == 'department')
            @if($setting->value == 'true')
                @php $department = 1; @endphp
            @else
                @php $department = 0 @endphp
            @endif  

        @elseif($setting->key == 'survey_setting')
            @if($setting->value == 'true')
                @php $surveySetting = 1; @endphp
            @else
                @php $surveySetting = 0 @endphp
            @endif  

      @endif     
    @endforeach

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  @if(Auth::user()->role != 'Customer' && Auth::user()->role != 'Owner')
    <!-- Brand Logo -->
    <a href="{{ route('dashboard', session('business_name')) }}" class="brand-link">
      <img src="/dist/img/AdminLTELogo.png" alt="Pawer System" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">{{ session('business_name') }}</span>
    </a>
  @endif
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          @php
            if(Auth::user()->profile_photo_path){
              $img = Auth::user()->profile_photo_path;
            }
            else{
              if(Auth::user()->gender == 'women'){ $img = 'avatar2.png'; }
              else{ $img =  'avatar5.png'; }
            }
          @endphp
          <img src="/images/{{ $img }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="{{ route('profile', session('business_name')) }}" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        @if(Auth::user()->role != 'Owner')
          <li class="nav-item has-treeview {{ (request()->is('MyTreatmentBookings','myEventBookings')) ? 'menu-open ' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('MyTreatmentBookings','myEventBookings')) ? 'active ' : '' }}">
              <i class="nav-icon fas fa-table"></i>
              <p>
                {{ __('leftnav.my_bookings') }} 
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            @if($treatmentBooking > 0)
              <li class="nav-item">
                <a href="{{ Route('MyTreatmentBookings',Session('business_name')) }}" class="nav-link {{ (request()->is('MyTreatmentBookings')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.treatment') }}</p>
                </a>
              </li>
            @endif
            @if($eventBooking > 0)  
              <li class="nav-item">
                <a href="{{ Route('myEventBookings',Session('business_name')) }}" class="nav-link {{ (request()->is('myEventBookings')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.events') }}</p>
                </a>
              </li>
            @endif  
            </ul>
          </li>
          @if($clipE || $clipT )
          <li class="nav-item has-treeview {{ (request()->is('myCards')) ? 'menu-open ' : '' }}">
            <a href="{{ Route('myCards',Session('business_name')) }}" class="nav-link {{ (request()->is('myCards')) ? 'active' : '' }}">
              <i class="nav-icon far fa-credit-card"></i>
              <p>
              {{ __('leftnav.my_cards') }}
              </p>
            </a>
          </li>
          @endif
        @endif

        @if( Auth::user()->role == "Owner" )   

          <li class="nav-item">
            <a href="{{ Route('requests',session('business_name')) }}" class="nav-link">
              <i class="nav-icon fas fa-envelope"></i>
              <p>
              {{ __('subscription.requests') }} ({{ $requests ?: 0 }})
              </p>
            </a>
          </li>

          <li class="nav-item has-treeview {{ (request()->is('business','businessView','createBusiness')) ? 'menu-open ' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('business','businessView','createBusiness')) ? 'active ' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
              {{ __('leftnav.locations') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ Route('business',Session('business_name')) }}" class="nav-link {{ (request()->is('business','businessView')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.view_all') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ Route('createBusiness',Session('business_name')) }}" class="nav-link {{ (request()->is('createBusiness')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.create_new') }}</p>
                </a>
              </li>
            </ul>
          </li>


          <li class="nav-item has-treeview {{ (request()->is('/plans/show','/plans/create','/plans/checkout')) ? 'menu-open ' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('/plans/show','/plans/create','/plans/checkout')) ? 'active ' : '' }}">
              <i class="nav-icon fa fa-th-large"></i>
              <p>
              {{ __('leftnav.plans') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ Route('plans.show',Session('business_name')) }}" class="nav-link {{ (request()->is('/plans/show')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.view_all') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ Route('plans.create',Session('business_name')) }}" class="nav-link {{ (request()->is('/plans/create')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.create_new') }}</p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="{{ Route('subscription.list',session('business_name')) }}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
              {{ __('subscription.subscriptions') }}
              </p>
            </a>
          </li>

          <li class="nav-item has-treeview {{ (request()->is('event-import','treatment-import','users-import')) ? 'menu-open ' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('event-import','treatment-import','users-import')) ? 'active ' : '' }}">
              <i class="nav-icon fas fa-database"></i>
              <p>
              {{ __('leftnav.import_data') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ Route('event-import',Session('business_name')) }}" class="nav-link {{ (request()->is('event-import')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.event_data') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ Route('treatment-import',Session('business_name')) }}" class="nav-link {{ (request()->is('treatment-import')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.treatment_data') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ Route('users-import',Session('business_name')) }}" class="nav-link {{ (request()->is('users-import')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{ __('leftnav.users_data') }}</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="{{ Route('country',session('business_name')) }}" class="nav-link">
              <i class="nav-icon fas fa-globe-europe"></i>
              <p>
              {{ __('leftnav.countries') }}
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ Route('showReport',session('business_name')) }}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
              {{ __('leftnav.reports') }}
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ Route('stats',session('business_name')) }}" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
              {{ __('leftnav.stats') }}
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ Route('logs',session('business_name')) }}" target="_blank" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Logging / Debugging
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ Route('calendar',session('business_name')) }}" class="nav-link">
              <i class="nav-icon far fa-calendar-alt"></i>
              <p>
                {{ __('leftnav.schedule') }}
              </p>
            </a>
          </li>
         
          <li class="nav-header">{{ __('leftnav.website_settings') }}</li>
          <li class="nav-item has-treeview ">
            <a href="#" class="nav-link {{ (request()->is('pagelist','editPage','brandInfo')) ? 'active' : '' }}">
              <i class="nav-icon fas fa-edit"></i>
              <p>
              {{ __('leftnav.website') }}
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview" style="display: none;">
              <li class="nav-item">
                <a href="{{ Route('pagelist',session('business_name')) }}" class="nav-link {{ (request()->is('pagelist')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon  text-danger"></i>
                  <p>{{ __('leftnav.web_pages') }}</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ Route('brandInfo',session('business_name')) }}" class="nav-link {{ (request()->is('brandInfo')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon text-info"></i>
                  <p>{{ __('leftnav.logo_brand_name') }}</p>
                </a>
              </li>
            </ul>
          </li>
          <!-- <li class="nav-header">{{ __('leftnav.system_settings') }}</li> -->

          <li class="nav-item has-treeview {{ (request()->is('database-business')) ? 'menu-open ' : '' }}">
            <a href="#" class="nav-link {{ (request()->is('database-business')) ? 'active ' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
              Database Tables
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ Route('database-business',Session('business_name')) }}" class="nav-link {{ (request()->is('database-business')) ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Business</p>
                </a>
              </li>
            </ul>
          </li>
        @endif  
    
        @if( Auth::user()->role != "Customer" && Auth::user()->role != "Owner" && Auth::user()->role != "owner" )
            {{-- @if(Auth::user()->role == "Super Admin") --}}
            <!-- This section is only for super admin -->
            <li class="nav-header">{{ __('leftnav.super_admin_section') }}</li>
            @canany(['Treatment Create','Treatment View','Treatment Edit','Date Create','Date View','Date Edit','Date Delete','Date Restore','Deleted Dates View','Deleted Dates Bookings View','Dates Booking Restore','Date Past Book','Date Past Bookings View'])

            @canany(['Date Bookings View','Date Create'])
              <li class="nav-item has-treeview {{ (request()->is('listtreatmentdate','creattreatmentdate','pastdatelist','treatmentDatesDeletedList')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('listtreatmentdate','creattreatmentdate','pastdatelist','treatmentDatesDeletedList')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-calendar-day"></i>
                  <p>
                    {{ __('leftnav.days') }} 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Date Bookings View')
                    <li class="nav-item">
                      <a href="{{ Route('listtreatmentdate',session('business_name')) }}" class="nav-link {{ (request()->is('listtreatmentdate')) ? 'active ' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>{{ __('leftnav.all_date_list') }}</p>
                      </a>
                    </li>
                  @endcan 
                  @can('Date Create') 
                    <li class="nav-item">
                      <a href="{{ Route('creattreatmentdate',session('business_name')) }}" class="nav-link {{ (request()->is('creattreatmentdate')) ? 'active ' : '' }}">
                        <i class="far fa-circle nav-icon"></i>
                        <p>{{ __('leftnav.create_new_date') }}</p>
                      </a>
                    </li>
                  @endcan
                </ul>
              </li>  
            @endcan

            @canany(['Date Past Bookings View','Deleted Dates Bookings View'])
              <li class="nav-item has-treeview {{ (request()->is('treatmentBookingBackward','treatmentDeletedBookings')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('treatmentDeletedBookings')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-calendar-day"></i>
                  <p>
                    {{ __('leftnav.bookings') }} 
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Date Past Bookings View') 
                  <li class="nav-item">
                    <a href="{{ Route('treatmentBookingBackward',session('business_name')) }}" class="nav-link {{ (request()->is('treatmentBookingBackward')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.add_bookings_backwards') }}</p>
                    </a>
                  </li>
                  @endcan
                  @can('Deleted Dates Bookings View') 
                  <li class="nav-item">
                    <a href="{{ Route('treatmentDeletedBookings',session('business_name')) }}" class="nav-link {{ (request()->is('treatmentDeletedBookings')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.deleted_bookings') }}</p>
                    </a>
                  </li>
                @endcan 
                </ul>
              </li>  
            @endcan

            <li class="nav-item has-treeview {{ (request()->is('treatmentlist','creattreatment','creattreatmentdate')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('treatmentlist','creattreatment','creattreatmentdate')) ? 'active ' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    {{ __('leftnav.treatment_section') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Treatment View')
                  <li class="nav-item">
                    <a href="{{ Route('treatmentlist',session('business_name')) }}" class="nav-link  {{ (request()->is('treatmentlist')) ? 'active ' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_treatment_list') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Treatment Create')  
                  <li class="nav-item">
                    <a href="{{ Route('creattreatment',session('business_name')) }}" class="nav-link {{ (request()->is('creattreatment')) ? 'active ' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new_treatment') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li> 
              @endcan
              @canany(['Event Create','Event Edit','Event Delete','Event List View','Deleted Events View','Deleted Events Bookings View','Event Restore','Event Booking Restore','Event Past Bookings View','Event Past Book'])
              <li class="nav-item has-treeview {{ (request()->is('createEvent','deletedEvents','eventsList','eventsDeleteBookings','eventBookingBackward')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('createEvent','deletedEvents','eventsList','eventsDeleteBookings','eventBookingBackward')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    {{ __('leftnav.event_section') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Event Create')
                  <li class="nav-item">
                    <a href="{{ Route('createEvent',session('business_name')) }}" class="nav-link {{ (request()->is('createEvent')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_event') }}</p>
                    </a>
                  </li>
                @endcan  
                @can('Event List View')
                  <li class="nav-item">
                    <a href="{{ Route('eventsList',session('business_name')) }}" class="nav-link {{ (request()->is('eventsList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.events_list') }}</p>
                    </a>
                  </li>
                @endcan 
                @can('Deleted Events View') 
                  <li class="nav-item">
                    <a href="{{ Route('deletedEvents',session('business_name')) }}" class="nav-link {{ (request()->is('deletedEvents')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.deleted_events') }}</p>
                    </a>
                  </li>
                @endcan 
                @can('Deleted Events Bookings View')  
                  <li class="nav-item">
                    <a href="{{ Route('eventsDeleteBookings',session('business_name')) }}" class="nav-link {{ (request()->is('eventsDeleteBookings')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.deleted_bookings') }}</p>
                    </a>
                  </li>
                @endcan 
                @can('Event Past Book')  
                  <li class="nav-item">
                    <a href="{{ Route('eventBookingBackward',session('business_name')) }}" class="nav-link {{ (request()->is('eventBookingBackward')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.add_bookings_backwards') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li> 
              @endcan

              @canany(['Role Create','Role Edit','Role View'])
              <li class="nav-item has-treeview {{ (request()->is('rolesList','rolesCreate')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('rolesList','rolesCreate')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    {{ __('leftnav.roles') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Role View')
                  <li class="nav-item">
                    <a href="{{ Route('rolesList',session('business_name')) }}" class="nav-link {{ (request()->is('rolesList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_roles_list') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Role Create')  
                  <li class="nav-item">
                    <a href="{{ Route('rolesCreate',session('business_name')) }}" class="nav-link {{ (request()->is('rolesCreate')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li> 
              @endcan

              @if($department)
              @canany(['Department Create','Department Edit','Department View','Department Delete'])
              <li class="nav-item has-treeview {{ (request()->is('departmentsList','createDepartment')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('departmentsList','createDepartment')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-building"></i>
                  <p>
                    {{ __('leftnav.departments') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Department View')
                  <li class="nav-item">
                    <a href="{{ Route('departmentsList',session('business_name')) }}" class="nav-link {{ (request()->is('departmentsList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_departments') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Department Create')  
                  <li class="nav-item">
                    <a href="{{ Route('createDepartment',session('business_name')) }}" class="nav-link {{ (request()->is('createDepartment')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li> 
              @endcan
              @endif
              @canany(['Payment Method View','Payment Method Create'])
              <li class="nav-item has-treeview {{ (request()->is('paymentMethodsList','createPaymentMethod')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('paymentMethodsList','createPaymentMethod')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-money-bill-wave"></i>
                  <p>
                    {{ __('leftnav.payment_method') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Payment Method View')
                  <li class="nav-item">
                    <a href="{{ Route('paymentMethodsList',session('business_name')) }}" class="nav-link {{ (request()->is('paymentMethodsList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_payment_methods') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Payment Method Create')  
                  <li class="nav-item">
                    <a href="{{ Route('createPaymentMethod',session('business_name')) }}" class="nav-link {{ (request()->is('createPaymentMethod')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li> 
              @endcan
              @canany(['Treatment Area View','Treatment Area Create'])
              <li class="nav-item has-treeview {{ (request()->is('treatmentPartsList','createTreatmentPart')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('treatmentPartsList','createTreatmentPart')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-child"></i>
                  <p>
                    {{ __('leftnav.treatment_areas') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Treatment Area View')
                  <li class="nav-item">
                    <a href="{{ Route('treatmentPartsList',session('business_name')) }}" class="nav-link {{ (request()->is('treatmentPartsList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_areas') }}</p>
                    </a>
                  </li>
                  @endcan
                  @can('Treatment Area Create')
                  <li class="nav-item">
                    <a href="{{ Route('createTreatmentPart',session('business_name')) }}" class="nav-link {{ (request()->is('createTreatmentPart')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new') }}</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>
              @endcan

              @canany(['Users Create','Users Edit','Users View','Users Delete','Users Change Password']) 
              <li class="nav-item has-treeview {{ (request()->is('adminlist','createadmin')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('adminlist','createadmin')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-users"></i>
                  <p>
                    {{ __('leftnav.users') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Users View') 
                  <li class="nav-item">
                    <a href="{{ Route('adminlist',session('business_name')) }}" class="nav-link {{ (request()->is('adminlist')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_users_list') }}</p>
                    </a>
                  </li>
                @endcan 
                @can('Users Create') 
                  <li class="nav-item">
                    <a href="{{ Route('createadmin',session('business_name')) }}" class="nav-link {{ (request()->is('createadmin')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new_user') }}</p>
                    </a>
                  </li>
                @endcan   
                </ul>
              </li>
            @endcan  
            @if($surveySetting)
        
            @canany(['Survey List View','Survey Question Create','Survey Question Edit','Survey Question View','Survey Show'])
              <li class="nav-item has-treeview {{ (request()->is('surveyList','surveyQuestionList','Survey')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('surveyList','surveyQuestionList','Survey')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-poll"></i>
                  <p>
                    {{ __('leftnav.survey') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Survey List View')
                  <li class="nav-item">
                    <a href="{{ Route('surveyList',session('business_name')) }}" class="nav-link {{ (request()->is('surveyList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.survey_list') }}</p>
                    </a>
                  </li>
                @endcan  
                @canany(['Survey Question Create','Survey Question View'])
                  <li class="nav-item">
                    <a href="{{ Route('surveyQuestionList',session('business_name')) }}" class="nav-link {{ (request()->is('surveyQuestionList')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.questions') }}</p>
                    </a>
                  </li>
                @endcan  
                @can('Survey Show')
                  {{-- <li class="nav-item">
                    <a href="{{ Route('Survey',[session('business_name'),'en']) }}" class="nav-link {{ (request()->is('Survey')) ? 'active' : '' }}" target="_blank">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.view_survey') }}</p>
                    </a>
                  </li> --}}
                  <li class="nav-item has-treeview {{ (request()->is('Survey')) ? 'menu-open ' : '' }}">
                    <a href="#" class="nav-link {{ (request()->is('Survey')) ? 'active' : '' }}">
                      <i class="nav-icon fas fa-poll"></i>
                      <p>
                        {{ __('leftnav.view_survey') }} 
                        <i class="fas fa-angle-left right"></i>
                      </p>
                    </a>
                    <ul class="nav nav-treeview">
                      <li class="nav-item">
                        <a href="{{ Route('Survey',[session('business_name'),'en']) }}" class="nav-link {{ (request()->is('Survey')) ? 'active' : '' }}" target="_blank">
                          <i class="far fa-circle nav-icon"></i>
                          <p>EN</p>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="{{ Route('Survey',[session('business_name'),'dk']) }}" class="nav-link {{ (request()->is('Survey')) ? 'active' : '' }}" target="_blank">
                          <i class="far fa-circle nav-icon"></i>
                          <p>DK</p>
                        </a>
                      </li>
                    </ul>
                  </li>  
                @endcan  
                </ul>
              </li>
            @endcan 
            @endif 
 
            @can('View Log')
              <li class="nav-item has-treeview {{ (request()->is('log','logSearch')) ? 'menu-open ' : '' }}">
                <a href="{{ Route('log',session('business_name')) }}" class="nav-link {{ (request()->is('log','logSearch')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    {{ __('keywords.logs_list') }}
                  </p>
                </a>
              </li> 
            @endcan
            
          <!-- This section is for Super Admin & Admin -->
          <li class="nav-header">{{ __('leftnav.admin_section') }}</li>  
            @canany(['Date Book','Date Bookings View'])
              <li class="nav-item has-treeview {{ (request()->is('treatmentbookings')) ? 'menu-open ' : '' }}">
                <a href="{{ Route('treatmentbookings',session('business_name')) }}" class="nav-link {{ (request()->is('treatmentbookings')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    {{ __('leftnav.booking') }} {{ __('leftnav.calendar') }}
                  </p>
                </a>
              </li>  
              {{-- <li class="nav-item has-treeview {{ (request()->is('treatmentbookings')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('treatmentbookings')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                  {{ __('leftnav.treatment') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ Route('treatmentbookings',session('business_name')) }}" class="nav-link {{ (request()->is('treatmentbookings')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.book_for_customer') }}</p>
                    </a>
                  </li>
                </ul>
              </li> --}}
            @endcan 
            @canany(['Event Booking View','Event Book'])  
              <li class="nav-item has-treeview {{ (request()->is('eventBookingList')) ? 'menu-open ' : '' }}">
                <a href="{{ Route('eventBookingList',session('business_name')) }}" class="nav-link {{ (request()->is('eventBookingList')) ? 'active ' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                   {{ __('leftnav.events') }} {{ __('leftnav.calendar') }}
                  </p>
                </a>
              </li>  
            {{-- <li class="nav-item has-treeview {{ (request()->is('eventBookingList')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('eventBookingList')) ? 'active ' : '' }}">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                  {{ __('leftnav.events') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ Route('eventBookingList',session('business_name')) }}" class="nav-link {{ (request()->is('eventBookingList')) ? 'active ' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.book_for_customer') }}</p>
                    </a>
                  </li>
                </ul>
              </li> --}}
            @endcan  
              @canany(['Card List View','Card Create','Card Edit','Card Clip Puchase'])
              @if($clipE || $clipT)
              <li class="nav-item has-treeview {{ (request()->is('cardList','addCard')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('cardList','addCard')) ? 'active ' : '' }}">
                  <i class="nav-icon far fa-credit-card"></i>
                  <p>
                    {{ __('leftnav.clipboard') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @can('Card List View')
                  <li class="nav-item">
                    <a href="{{ Route('cardList',session('business_name')) }}" class="nav-link {{ (request()->is('cardList')) ? 'active ' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.card_list') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Card Create')  
                  <li class="nav-item">
                  <a href="{{ Route('addCard',session('business_name')) }}" class="nav-link {{ (request()->is('addCard')) ? 'active ' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.add_card') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li>
              @endif
              @endcan
              
              @canany(['Customer Create','Customer Edit','Customer View','Customer Delete'])
              <li class="nav-item has-treeview {{ (request()->is('customerlist','createcustomer')) ? 'menu-open ' : '' }}">
                <a href="#" class="nav-link {{ (request()->is('customerlist','createcustomer')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p>
                    {{ __('leftnav.customers') }}
                    <i class="fas fa-angle-left right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                @canany(['Customer Edit','Customer View','Customer Delete'])
                  <li class="nav-item">
                    <a href="{{ Route('customerlist',session('business_name')) }}" class="nav-link {{ (request()->is('customerlist')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.all_customers_list') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Customer Create')  
                  <li class="nav-item">
                    <a href="{{ Route('createcustomer',session('business_name')) }}" class="nav-link {{ (request()->is('createcustomer')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon"></i>
                      <p>{{ __('leftnav.create_new_customer') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
              </li>
              @endcan
              @can('Journal List View')
              <li class="nav-item">
                <a href="{{ Route('journalList',session('business_name')) }}" class="nav-link {{ (request()->is('journalList')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-book"></i>
                  <p>
                    {{ __('leftnav.journal') }}
                  </p>
                </a>
              </li>
              @endcan
              @canany(['Email List View','Email Create','Email View','Email Delete'])
              <li class="nav-item">
              <a href="{{ Route('emailList',session('business_name')) }}" class="nav-link {{ (request()->is('emailList')) ? 'active' : '' }}">
                  <i class="nav-icon far fa-envelope"></i>
                  <p>
                    {{ __('leftnav.send_email') }}
                  </p>
                </a>
              </li>
              @endcan
              @canany(['Reports Users View','Reports Booking View','Reports Date View','Reports Unique User View','Reports Srvey View'])
              <li class="nav-item">
                <a href="{{ Route('showReport',session('business_name')) }}" class="nav-link">
                  <i class="nav-icon fas fa-th"></i>
                  <p>
                  {{ __('leftnav.reports') }}
                  </p>
                </a>
              </li>
              @endcan
              @can('Stats View')
              <li class="nav-item">
                <a href="{{ Route('stats',session('business_name')) }}" class="nav-link">
                  <i class="nav-icon fas fa-th"></i>
                  <p>
                  {{ __('leftnav.stats') }}
                  </p>
                </a>
              </li> 
              @endcan
              @canany(['Website Pages View', 'Brand Details View'])
              <li class="nav-header">{{ __('leftnav.website_settings') }}</li>
              <li class="nav-item has-treeview">
                <a href="#" class="nav-link {{ (request()->is('pagelist','editPage','brandInfo')) ? 'active' : '' }}">
                  <i class="nav-icon fas fa-edit"></i>
                  <p>
                  {{ __('leftnav.website') }}
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                @can('Website Pages View')
                  <li class="nav-item">
                    <a href="{{ Route('pagelist',session('business_name')) }}" class="nav-link  {{ (request()->is('pagelist')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon text-danger"></i>
                      <p>{{ __('leftnav.web_pages') }}</p>
                    </a>
                  </li>
                @endcan
                @can('Brand Details View')  
                  <li class="nav-item">
                    <a href="{{ Route('brandInfo',session('business_name')) }}" class="nav-link {{ (request()->is('brandInfo')) ? 'active' : '' }}">
                      <i class="far fa-circle nav-icon text-info"></i>
                      <p>{{ __('leftnav.logo_brand_name') }}</p>
                    </a>
                  </li>
                @endcan  
                </ul>
            </li>
          @endcan
          @can('Settings View')
          <!-- Only for super admin -->
            <li class="nav-header">{{ __('leftnav.system_settings') }}</li>
              <li class="nav-item has-treeview">
                <a href="{{ Route('settingList',session('business_name')) }}" class="nav-link {{ (request()->is('settingList')) ? 'active' : '' }}" class="nav-link">
                <i class=" nav-icon far fa-caret-square-down"></i>
                  <p>
                    {{ __('leftnav.settings') }}
                  </p>
                </a>
              </li>              
          @endcan  
          
          @can('Billing View')
          <!-- Only for super admin -->
            <li class="nav-header">{{ __('leftnav.billing') }}</li>
              <li class="nav-item has-treeview">
                <a href="{{ Route('plans.show',session('business_name')) }}" class="nav-link {{ (request()->is('subscription/list','plans/show')) ? 'active' : '' }}" class="nav-link">
                <i class=" nav-icon far fa-caret-square-down"></i>
                  <p>
                    {{ __('subscription.subscription') }}
                  </p>
                </a>
              </li>              
          @endcan  
        @endif

        {{-- @if(Auth::user()->role == 'Owner')
        <li class="nav-header">{{ __('leftnav.system_settings') }}</li>
          <li class="nav-item has-treeview">
            <a href="{{ Route('importCustomer',session('business_name')) }}" class="nav-link {{ (request()->is('importCustomer')) ? 'active' : '' }}" class="nav-link">
            <i class=" nav-icon far fa-caret-square-down"></i>
              <p>
                Upload Customers Data
              </p>
            </a>
          </li>
        @endif --}}

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>