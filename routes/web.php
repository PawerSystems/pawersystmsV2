<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\TreatmentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\TreatmentPartController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ImportDataController;
use App\Http\Controllers\DbController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\LocationLogController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::group(['domain' => config('app.domain')], function()
{
    
    Route::any('/ics/{data}/{calendar}',function($data,$calendar){
        $url = $data.'/'.$calendar;
        echo "<script>window.open('".$url."');window.close();</script>";
        exit();
    });
    Route::any('/webhooks/smsStatus',[SmsController::class, 'statusCheck'])->name('/webhooks/smsStatus');

    Route::get('/unsubscribe/{user}',[UserProfileController::class, 'unsub'])->name('unsubscribe')->middleware('signed');
    Route::post('/unsubUser',[UserProfileController::class, 'unsubUser'])->name('unsubUser');


    Route::get('clear_cache', function () {
        \Artisan::call('config:cache'); 
        \Log::channel('custom')->info("Cache Cleared.");   
    });

    Route::get('free_spot', function () {
        \Artisan::call('free:spot'); 
        \Log::channel('custom')->info("Send free spot email");   
    });


    Route::group(['middleware' => ['setsession']], function () {

        Route::any('/', function(){ return view('welcome'); }); 
        Route::post('logged_in', [LoginController::class, 'authenticate'])->name('logged_in');
        Route::get('/smsStatus',[SmsController::class, 'statusCheck'])->name('smsStatus');
        
        Route::group(['middleware' => ['auth:sanctum', 'verified','systemuser']], function () {
            Route::get('/dashboard', function() { return view('dashboard'); })->name('dashboard');

            //----------- User Pofile Routes ------------
            Route::get('/profile', [UserProfileController::class,'view'] )->name('profile'); 
            Route::post('/updateProfile',[UserProfileController::class,'update'])->name('updateProfile');
            Route::post('/passreset',[UserProfileController::class,'passwordReset'])->name('passreset');

            //----------- Business Controller Routes ------------
            Route::group(['middleware' => ['owner']], function () {

                Route::get('/business', [BusinessController::class,'index'] )->name('business'); 
                Route::get('/createBusiness', [BusinessController::class,'create'] )->name('createBusiness'); 
                Route::post('/registerBusiness', [BusinessController::class,'register'] )->name('registerBusiness'); 
                Route::get('/businessView/{id}', [BusinessController::class,'view'] )->name('businessView'); 
                
            });

        });

        //------ When user not active or not part of current system ----------
        Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
            Route::any('/deactive', function() { return view('deactive'); }); 
        });
    });
    
}); 

Route::group(['domain' => '{subdomain}.'.config('app.domain')], function()
{
    Route::get('/owneroles',[BusinessController::class, 'temp'])->name('owneroles');     

    Route::any('/ics/{data}/{calendar}',function($lang,$data,$calendar){
        $url = $data.'/'.$calendar;
        echo "<script>window.open('".$url."');window.close();</script>";
        exit();
    });

    Route::any('/getfile/{filename}', function($lang,$filename){
        return \Storage::get('public/ics/'.$filename); 
    });


    Route::post('/changeDateFormat',[WebsiteController::class,'changeDate'])->name('changeDateFormat');
    Route::get('/mobile-pay/{string}',[WebsiteController::class,'mobilePayLink'])->name('mobile-pay');

    Route::any('/webhooks/smsStatus',[SmsController::class, 'statusCheck'])->name('/webhooks/smsStatus');
    Route::any('clear_cache', function () {
        \Artisan::call('config:cache'); 
        \Log::channel('custom')->info("Cache Cleared.");   
    });

    Route::any('free_spot', function () {
        \Artisan::call('free:spot'); 
        \Log::channel('custom')->info("Send free spot email");   
    });
    //------ Fo langage switcher -----//
    Route::get('/lang/{key}',function($language,$key){
        session(['locale' =>  $key]);
        return redirect()->back();
    })->name('lang');

    Route::get('/unsubscribe/{user}',[UserProfileController::class, 'unsub'])->name('unsubscribe');
    Route::post('/unsubUser',[UserProfileController::class, 'unsubUser'])->name('unsubUser');

    Route::group(['middleware' => ['setsession']], function ($subdomain) {

        Route::get('/other-account/{user}/{medium?}',[BusinessController::class, 'switch'])->name('other-account');  
        
        //--------------------- Email routes -----------------
        Route::get('/email', [EmailController::class,'create'])->name('email');
        Route::post('/email', [EmailController::class,'sendEmail'])->name('send.email');
        
        //Route::any('/', function($subdomain){ return view('web.home'); });
        Route::get('/Survey/{language}',[SurveyController::class,'ViewSurvey'])->name('Survey');
        Route::post('/saveSurvey',[SurveyController::class,'saveSurvey'])->name('saveSurvey');

        Route::any('/home', [WebsiteController::class,'index'])->name('home');
        Route::any('/', [WebsiteController::class,'index']);
        Route::any('/information', [WebsiteController::class,'index'])->name('information');
        Route::any('/main', [WebsiteController::class,'index'])->name('main');
        Route::any('/booking', [WebsiteController::class,'booking'])->name('booking');
        Route::any('/events', [WebsiteController::class,'keepBooking'])->name('events');
        Route::any('/status', [WebsiteController::class,'status'])->name('status');
        Route::any('/prices', [WebsiteController::class,'prices'])->name('prices');
        Route::any('/gdpr', [WebsiteController::class,'gdpr'])->name('gdpr');
        Route::any('/covid', [WebsiteController::class,'covid'])->name('covid');
        Route::any('/resendemail', [WebsiteController::class,'resendEmail'])->name('resendemail');
        Route::any('/insurance', [WebsiteController::class,'termForInsurance'])->name('insurance');
        
        Route::get('/slet', function(){
            return redirect()->route('login');
        })->name('slet');
        Route::get('/adm', function(){
            return redirect()->route('login');
        })->name('adm');

        Route::post('logged_in', [LoginController::class, 'authenticate'])->name('logged_in');

        //---------------- Get user data with number only ---------
        Route::post('/getUserDataWithNumberAjax',[TreatmentController::class,'getUserDataWithNumberAjax'] )->name('getUserDataWithNumberAjax');

        //---------------- Book event from website -------------
        Route::post('/eventBookFromSiteAjax',[WebsiteController::class,'eventBookFromSiteAjax'] )->name('eventBookFromSiteAjax');
        
        //------------------- Get dates from treatment ------------------------------
        Route::post('/getDateOfTreatmentAjax',[WebsiteController::class,'getDateOfTreatmentAjax'] )->name('getDateOfTreatmentAjax');

        //------------------- Get therapists from treatment ------------------------------
        Route::post('/getTherapistOfTreatmentAjax',[WebsiteController::class,'getTherapistOfTreatmentAjax'] )->name('getTherapistOfTreatmentAjax');
        
        //------ Book user for treatment ---------------
        Route::post('/treatmentBookFromSiteAjax',[WebsiteController::class,'treatmentBookFromSiteAjax'] )->name('treatmentBookFromSiteAjax');
        
        Route::post('/BookTimeSlotForWebAjax',[TreatmentController::class,'BookTimeSlotForWebAjax'] )->name('BookTimeSlotForWebAjax');

        Route::post('/BookWaitingTimeSlotWebAjax',[TreatmentController::class,'BookWaitingTimeSlotWebAjax'] )->name('BookWaitingTimeSlotWebAjax');

        Route::get('/contact/{msg?}', [WebsiteController::class,'contact'])->name('contact');
        Route::any('/contactSave', [WebsiteController::class,'contactSave'])->name('contactSave');

        //------------------- Only for active users ------------------------------
        Route::group(['middleware' => ['auth:sanctum', 'verified','active','systemuser','ProfileCompletionCheck']], function ($subdomain) {

            //------------ temp settings -------
            Route::get('/settingGenerate', [BusinessController::class,'generateSettings'])->name('settingGenerate');
            Route::get('/pagesGenerate', [BusinessController::class,'generatePages'])->name('pagesGenerate');


            Route::get('/dashboard', [StatsController::class,'dashboard'])->name('dashboard');
            Route::post('/change-user',[BusinessController::class, 'changeUser'])->name('change-user');  
            //------ Routes for customers -----
            Route::get('/MyTreatmentBookings',[CustomerController::class,'myTreatmentBooking'] )->name('MyTreatmentBookings');
            Route::get('/myEventBookings',[CustomerController::class,'myEventBooking'] )->name('myEventBookings');
            Route::get('/myCards',[CustomerController::class,'myCards'] )->name('myCards');

            //----------- Reports Routes ------------
            Route::get('/showReport', [ReportsController::class,'index'] )->name('showReport'); 
            Route::post('/getReport', [ReportsController::class,'displayReport'] )->name('getReport'); 
            Route::post('/schedule-report', [ReportsController::class,'scheduleReport'] )->name('schedule-report'); 
            Route::get('/scheduled-reports', [ReportsController::class,'scheduledReports'] )->name('scheduled-reports'); 
            Route::get('/report-edit/{id}', [ReportsController::class,'edit'] )->name('report-edit');
            Route::post('/schedule-report-edit', [ReportsController::class,'ReportEdit'] )->name('schedule-report-edit'); 
 
            

            
            //----------- Stats Routes ------------
            Route::get('/stats', [StatsController::class,'index'] )->name('stats'); 
            
            //----------- Journal Route -------------------
            Route::get('/journalList',[JournalController::class,'index'] )->name('journalList');
            Route::get('/journal/{id}',[JournalController::class,'userJournal'] )->name('journal');
            Route::post('/addJournal',[JournalController::class,'addAjax'])->name('addJournal');
            Route::post('/updateAjax',[JournalController::class,'updateAjax'])->name('updateAjax');

            //----------- Email Route -------------------
            Route::get('/emailList',[EmailController::class,'list'] )->name('emailList');
            Route::post('/addEmail',[EmailController::class,'add'])->name('addEmail');
            Route::get('/viewEmail/{id}',[EmailController::class,'view'] )->name('viewEmail');
            Route::post('/deleteEmail',[EmailController::class,'delete'] )->name('deleteEmail');

        
            //----------- User Pofile Routes ------------
            Route::get('/showUser/{id}', [UserProfileController::class,'show'] )->name('showUser'); 
            Route::get('/profile', [UserProfileController::class,'view'] )->name('profile'); 
            Route::post('/updateProfile',[UserProfileController::class,'update'])->name('updateProfile');
            Route::post('/passreset',[UserProfileController::class,'passwordReset'])->name('passreset');
            Route::post('/subUser',[UserProfileController::class, 'subUser'])->name('subUser');
            Route::post('/disableuser',[UserProfileController::class, 'disableUser'])->name('disableuser');
            Route::post('/enableuser',[UserProfileController::class, 'enableUser'])->name('enableuser');
            Route::post('/updateTharapist',[UserProfileController::class, 'updateTharapist'])->name('updateTharapist');

            
            //----------- Location logs Routes ------------
            Route::get('/log', [LocationLogController::class,'index'] )->name('log'); 
            Route::any('/logSearch', [LocationLogController::class,'logSearch'] )->name('logSearch'); 
            
            //------------ Website pages Routes for backend ----------------------
            Route::get('/pagelist', [WebsiteController::class,'pageList'] )->name('pagelist'); 
            Route::get('/editPage/{id}', [WebsiteController::class,'editPage'] )->name('editPage'); 
            Route::post('/savePage', [WebsiteController::class,'savePage'] )->name('savePage');
            Route::get('/brandInfo', [WebsiteController::class,'brandInfo'] )->name('brandInfo'); 
            Route::post('/updateBrand', [WebsiteController::class,'update'] )->name('updateBrand');
            Route::post('/addLink', [WebsiteController::class,'addLink'] )->name('addLink');

            //------------- Customer Routes -------------------------------
            Route::get('/customerlist', [CustomerController::class,'index'] )->name('customerlist'); 
            Route::get('/disablecustomerlist', [CustomerController::class,'disableCustomers'] )->name('disablecustomerlist'); 
            Route::get('/createcustomer', [CustomerController::class,'create'] )->name('createcustomer'); 
            Route::post('/registerCustomer', [CustomerController::class,'save'] )->name('registerCustomer'); 
            Route::get('/editcustomer/{id}', [CustomerController::class,'edit'] )->name('editcustomer'); 
            Route::post('/updatecustomer', [CustomerController::class,'update'] )->name('updatecustomer');
            Route::post('/deletecustomer', [CustomerController::class,'delete'] )->name('deletecustomer');
            Route::get('/changepasscustomer/{id}', [CustomerController::class,'changePass'] )->name('changepasscustomer'); 
            Route::post('/updatecustomerpass', [CustomerController::class,'updatePass'] )->name('updatecustomerpass');

            Route::post('/addCustomer',[CustomerController::class,'add'])->name('addCustomer');//-- from treatment slot and event slot page ---

            //------------- Treatment Routes -------------------------------
            Route::get('/treatmentbookings/{id?}',[TreatmentController::class,'treatmentSlotsList'] )->name('treatmentbookings');
            Route::post('/BookTimeSlot',[TreatmentController::class,'BookTimeSlotAjax'] )->name('BookTimeSlot');
            Route::post('/BookWaitingTimeSlot',[TreatmentController::class,'BookWaitingTimeSlotAjax'] )->name('BookWaitingTimeSlot');
            Route::post('/DeleteBookWaitingTimeSlot',[TreatmentController::class,'DeleteBookWaitingTimeSlotAjax'] )->name('DeleteBookWaitingTimeSlot');
            
            Route::post('/DeleteBooking',[TreatmentController::class,'DeleteBookingAjax'] )->name('DeleteBooking');
            Route::post('/getDateData',[TreatmentController::class,'getDateData'])->name('getDateData');
            Route::post('/updatePaymentPartAjax',[TreatmentController::class,'updatePaymentPartAjax'])->name('updatePaymentPartAjax');
            Route::post('/sendMobilePaySms',[TreatmentController::class,'sendMobilePaySms_'])->name('sendMobilePaySms');
            Route::post('/getAvailableTreatments',[TreatmentController::class,'getAvailableTreatments'])->name('getAvailableTreatments');            
            
            //---------------- Get user data of current system ---------
            Route::post('/getUserDataAjax',[TreatmentController::class,'getUserDataAjax'] )->name('getUserDataAjax');

            //----------------- Card Routes ---------------------------------------------
            Route::get('/cardList',[CardController::class,'list'])->name('cardList');
            Route::get('/addCard',[CardController::class,'add'])->name('addCard');
            Route::post('/createCard',[CardController::class,'create'])->name('createCard');
            Route::get('/editCard/{id}',[CardController::class,'edit'])->name('editCard');
            Route::post('/updateCard',[CardController::class,'update'])->name('updateCard');
            Route::post('/updateCardAjax',[CardController::class,'updateAjax'])->name('updateCardAjax');
            Route::post('/getCardUsedClipsAjax',[CardController::class,'getCardUsedClipsAjax'])->name('getCardUsedClipsAjax');
            Route::post('/bookClipsAjax',[CardController::class,'bookClipsAjax'])->name('bookClipsAjax');
            Route::post('/deleteClipAjax',[CardController::class,'deleteClipAjax'])->name('deleteClipAjax');
            
            //---------------------- Events Routes -----------------------------------
            Route::get('/eventBookingList',[EventController::class,'slots'])->name('eventBookingList');
            Route::post('/BookEvent',[EventController::class,'book'])->name('BookEvent');
            Route::post('/deleteEventBooking',[EventController::class,'delete'])->name('deleteEventBooking');           
            
            //------------- Super Admin Routes -------------------------------
            //Route::group(['middleware' => ['superadmin']], function ($subdomain) {
                Route::get('/adminlist', [AdminController::class,'index'] )->name('adminlist'); 
                Route::get('/disableAdminlist', [AdminController::class,'disableUsers'] )->name('disableAdminlist');                
                Route::get('/createadmin', [AdminController::class,'create'] )->name('createadmin'); 
                Route::post('/registerAdmin', [AdminController::class,'save'] )->name('registerAdmin'); 
                Route::get('/editadmin/{id}', [AdminController::class,'edit'] )->name('editadmin'); 
                Route::post('/updateadmin', [AdminController::class,'update'] )->name('updateadmin');
                Route::get('/changepassadmin/{id}', [AdminController::class,'changePass'] )->name('changepassadmin'); 
                Route::post('/updateadminpass', [AdminController::class,'updatePass'] )->name('updateadminpass');
                Route::post('/deleteadmin', [AdminController::class,'delete'] )->name('deleteadmin'); 

                //------------- Treatment Routes -------------------------------
                Route::get('/treatmentlist', [TreatmentController::class,'treatmentList'] )->name('treatmentlist'); 
                Route::get('/treatmentDeletedBookings', [TreatmentController::class,'treatmentDeletedBookingsList'] )->name('treatmentDeletedBookings'); 
                Route::post('/RestoreTreatmentBookingAjax', [TreatmentController::class,'RestoreTreatmentBookingAjax'] )->name('RestoreTreatmentBookingAjax'); 
                Route::get('/creattreatment', [TreatmentController::class,'creatTreatment'] )->name('creattreatment'); 
                Route::post('/addTreatment', [TreatmentController::class,'addTreatment'] )->name('addTreatment');
                Route::get('/edittreatment/{id}', [TreatmentController::class,'editTreatment'] )->name('edittreatment');
                Route::get('/deletetreatment/{id}', [TreatmentController::class,'deleteTreatment'] )->name('deletetreatment'); 
                Route::post('/updatetreatment', [TreatmentController::class,'updateTreatment'] )->name('updatetreatment');
                Route::get('/treatmentBookingBackward',[TreatmentController::class,'BookingBackward'])->name('treatmentBookingBackward');
                Route::post('/AddBreak',[TreatmentController::class,'AddBreakAjax'] )->name('AddBreak');
                Route::post('/getTherapists',[TreatmentController::class,'getTherapistsAjax'] )->name('getTherapists');
                Route::post('/updateTreatmentId',[TreatmentController::class,'updateTreatmentId'] )->name('updateTreatmentId');
                
                //----------------- Date Routes ------------------------------               
                Route::get('/creattreatmentdate', [TreatmentController::class,'createTreatmentDates'] )->name('creattreatmentdate');
                Route::post('/savetreatmentdate', [TreatmentController::class,'saveTreatmentDate'] )->name('savetreatmentdate');
                Route::get('/listtreatmentdate', [TreatmentController::class,'treatmentDatesList'] )->name('listtreatmentdate'); 
                Route::post('/updateDateAjax', [TreatmentController::class,'updateDateAjax'] )->name('updateDateAjax'); 
                Route::post('/deleteDateAjax', [TreatmentController::class,'deleteDateAjax'] )->name('deleteDateAjax'); 
                Route::get('/treatmentDatesDeletedList', [TreatmentController::class,'treatmentDatesDeletedList'] )->name('treatmentDatesDeletedList'); 
                Route::get('/pastdatelist', [TreatmentController::class,'pastdatelist'] )->name('pastdatelist'); 
                Route::post('/restoreDateAjax', [TreatmentController::class,'restoreDateAjax'] )->name('restoreDateAjax');
                Route::post('/checkCPForInsurance', [TreatmentController::class,'checkCPForInsurance'] )->name('checkCPForInsurance');
                
                //---------------------- Events Routes -----------------------------------
                Route::get('/createEvent',[EventController::class,'create'])->name('createEvent');
                Route::post('/saveEvent',[EventController::class,'save'])->name('saveEvent');
                Route::get('/eventsList/{var?}',[EventController::class,'list'])->name('eventsList');
                Route::get('/editEvent/{id}',[EventController::class,'edit'])->name('editEvent');
                Route::post('/deleteEvent',[EventController::class,'deleteEvent'])->name('deleteEvent');
                Route::post('/updateEvent',[EventController::class,'update'])->name('updateEvent');
                Route::get('/eventsDeleteBookings',[EventController::class,'deletedSlots'])->name('eventsDeleteBookings');
                Route::post('/RestoreEventBookingAjax',[EventController::class,'RestoreEventBookingAjax'])->name('RestoreEventBookingAjax');
                Route::get('/eventBookingBackward',[EventController::class,'pastEvents'])->name('eventBookingBackward');
                Route::get('/deletedEvents',[EventController::class,'deletedEvents'])->name('deletedEvents');
                Route::post('/restoreEvent',[EventController::class,'restoreEvent'])->name('restoreEvent');
                Route::post('/getEventTherapists',[EventController::class,'getTherapistsAjax'] )->name('getEventTherapists');

                //------- Payment Methods Routes ---------------
                Route::get('/paymentMethodsList',[PaymentMethodController::class,'list'])->name('paymentMethodsList');
                Route::get('/createPaymentMethod',[PaymentMethodController::class,'create'])->name('createPaymentMethod');
                Route::post('/addMethod',[PaymentMethodController::class,'add'])->name('addMethod');
                Route::post('/updateMethodAjax',[PaymentMethodController::class,'updateMethodAjax'])->name('updateMethodAjax');
                Route::post('/updateStatusAjax',[PaymentMethodController::class,'updateStatusAjax'])->name('updateStatusAjax');
                Route::post('/deleteMethodAjax',[PaymentMethodController::class,'deleteAjax'])->name('deleteMethodAjax');

                 //------- Treatment Part Routes ---------------
                 Route::get('/treatmentPartsList',[TreatmentPartController::class,'list'])->name('treatmentPartsList');
                 Route::get('/createTreatmentPart',[TreatmentPartController::class,'create'])->name('createTreatmentPart');
                 Route::post('/addPart',[TreatmentPartController::class,'add'])->name('addPart');
                 Route::post('/updatePartAjax',[TreatmentPartController::class,'updatePartAjax'])->name('updatePartAjax');
                 Route::post('/updatePartStatusAjax',[TreatmentPartController::class,'updatePartStatusAjax'])->name('updatePartStatusAjax');
                 Route::post('/deletePartAjax',[TreatmentPartController::class,'deleteAjax'])->name('deletePartAjax');

                 Route::group(['middleware' => ['departmentCheck']], function ($subdomain) {
                    //------- Depatment Routes ---------------
                    Route::get('/departmentsList',[DepartmentController::class,'list'])->name('departmentsList');
                    Route::get('/createDepartment',[DepartmentController::class,'create'])->name('createDepartment');
                    Route::post('/addDepartment',[DepartmentController::class,'add'])->name('addDepartment');
                    Route::post('/updateDepartmentAjax',[DepartmentController::class,'updateDepartmentAjax'])->name('updateDepartmentAjax');
                    Route::post('/updateDepStatusAjax',[DepartmentController::class,'updateStatusAjax'])->name('updateDepStatusAjax');
                    Route::post('/deleteDepartmentAjax',[DepartmentController::class,'deleteAjax'])->name('deleteDepartmentAjax');
                });  
                
                //---------------------- PDF Routes ------------------------//
                Route::get('/receipt-send/{id}',[PdfController::class,'sendReceipt'])->name('receipt-send');
                Route::get('/pdf-create',[PdfController::class,'create'])->name('pdf-create');
                Route::get('/view-receipt/{file}',function($subdomain,$file) { 
                    return response()->file( base_path().'/public/receipts/'.$file); 
                })->name('view-receipt');

                //---------------------- Role Routes ---------------------//
                Route::get('/rolesList',[RoleController::class,'list'])->name('rolesList');
                Route::get('/rolesCreate',[RoleController::class,'create'])->name('rolesCreate');
                Route::post('/saveRole',[RoleController::class,'save'])->name('saveRole');
                Route::get('/edirole/{id}',[RoleController::class,'edit'])->name('edirole');
                Route::post('/updateRole',[RoleController::class,'update'])->name('updateRole');
                Route::post('/deleterole',[RoleController::class,'delete'])->name('deleterole');
                
                //---------- System Settings ------------------
                Route::get('/settingList',[SettingController::class,'list'])->name('settingList');
                Route::post('/updateSetting',[SettingController::class,'update'])->name('updateSetting');
                Route::post('/fetch-offer-data',[SettingController::class,'fetchOfferData'])->name('fetch-offer-data');
                Route::post('/check-free-treatments',[SettingController::class,'checkFreeTreatment'])->name('check-free-treatments');
                
                Route::post('/navSettings',[SettingController::class,'navSettings'])->name('navSettings');

                //---------- Survey Settings ------------------
                Route::get('/surveyList',[SurveyController::class,'list'])->name('surveyList');
                Route::get('/surveyQuestionList',[SurveyController::class,'questionsList'])->name('surveyQuestionList');
                Route::get('/addQuestion',[SurveyController::class,'addQuestionsOptions'])->name('addQuestion');
                Route::post('/saveQuestion',[SurveyController::class,'saveQuestionsOptions'])->name('saveQuestion');
                Route::get('/editQuestion/{id}', [SurveyController::class,'edit'] )->name('editQuestion');
                Route::post('/updateQuestion',[SurveyController::class,'updateQuestionsOptions'])->name('updateQuestion');
                Route::post('/showSurvey',[SurveyController::class,'show'])->name('showSurvey');

                Route::get('/calendar', [CalendarController::class,'view'] )->name('calendar'); 
                Route::get('/calendarUserWise', [CalendarController::class,'viewUserWise'] )->name('calendarUserWise'); 

            //}); 

            //--------------- Strip payment routes ---------------//
            Route::get('/plans/show', [SubscriptionController::class,'showPlans'])->name('plans.show');
            Route::get('/plans/checkout/{planID}', [SubscriptionController::class,'checkout'] )->name('plans.checkout'); 
            Route::post('/plans/process', [SubscriptionController::class,'processSubscription'] )->name('plans.process'); 
            Route::get('/subscription/list', [SubscriptionController::class,'allSubscriptions'] )->name('subscription.list'); 
            Route::get('/subscription/resume', [SubscriptionController::class,'resumeSubscriptions'] )->name('subscription.resume'); 
            Route::get('/subscription/cancel', [SubscriptionController::class,'cancelSubscriptions'] )->name('subscription.cancel'); 


            Route::get('/invoices/list/{subscription}', [SubscriptionController::class,'viewInvoices'] )->name('invoices.list'); 
            Route::get('/invoice/{invoice}', [SubscriptionController::class,'downloadInvoice'])->name('invoice.download');


            // Route::any('/plans',function(){
            //     return view('stripe.plans');
            // }); 

            //----------- Business Controller Routes ------------
            Route::group(['middleware' => ['owner']], function ($subdomain) {

                Route::get('/business', [BusinessController::class,'index'] )->name('business'); 
                Route::get('/createBusiness', [BusinessController::class,'create'] )->name('createBusiness'); 
                Route::post('/registerBusiness', [BusinessController::class,'register'] )->name('registerBusiness'); 
                Route::get('/businessView/{id}', [BusinessController::class,'view'] )->name('businessView'); 
                Route::get('/businessEdit/{id}', [BusinessController::class,'edit'] )->name('businessEdit'); 
                Route::post('/editBusiness', [BusinessController::class,'editSave'] )->name('editBusiness'); 
                
                //---------- Calendar Controller Routes ------
                // Route::get('/calendar', [CalendarController::class,'view'] )->name('calendar'); 
                // Route::get('/calendarUserWise', [CalendarController::class,'viewUserWise'] )->name('calendarUserWise'); 

                //---------- Countries Controller Routes ------
                Route::get('/country', [CountryController::class,'list'] )->name('country'); 
                Route::get('/creatCountry', [CountryController::class,'create'] )->name('creatCountry');
                Route::post('/registerCountry', [CountryController::class,'save'] )->name('registerCountry'); 
                Route::get('/CountryEdit/{id}', [CountryController::class,'edit'] )->name('CountryEdit'); 
                Route::post('/editCountry', [CountryController::class,'editSave'] )->name('editCountry'); 
                Route::get('/CountryDelete/{id}', [CountryController::class,'delete'] )->name('CountryDelete');

                //---------- Countries Controller Routes ------
                Route::get('/event-import', [ImportDataController::class,'eventPage'] )->name('event-import'); 
                Route::get('/treatment-import', [ImportDataController::class,'treatmentPage'] )->name('treatment-import');
                Route::get('/users-import', [ImportDataController::class,'usersPage'] )->name('users-import'); 

                Route::post('/import-treatment-data', [ImportDataController::class,'importTreatmentData'] )->name('import-treatment-data');
                Route::post('/import-event-data', [ImportDataController::class,'importEventData'] )->name('import-event-data'); 
                Route::post('/import-users-data', [ImportDataController::class,'importUserData'] )->name('import-users-data'); 
                // Route::get('/CountryDelete/{id}', [CountryController::class,'delete'] )->name('CountryDelete'); 

                //------------ Database routes -------------//
                Route::get('/database-business', [DbController::class,'businessDB'] )->name('database-business'); 
                Route::post('/database-business-add', [DbController::class,'businessDBAdd'] )->name('database-business-add'); 
                Route::post('/database-business-edit', [DbController::class,'businessDBEdit'] )->name('database-business-edit'); 
                Route::post('/database-business-delete', [DbController::class,'businessDBDelete'] )->name('database-business-delete'); 

                Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class,'index'])->name('logs');
                
                //--------------- Strip payment routes ---------------//
                // Route::get('/subscriptions', [SubscriptionController::class,'index'] )->name('subscriptions'); 
                // Route::post('/single-charge', [SubscriptionController::class,'singleCharge'] )->name('single.charge'); 

                Route::get('/plans/create', [SubscriptionController::class,'createPlan'])->name('plans.create');
                Route::post('/plans/save', [SubscriptionController::class,'savePlan'] )->name('plans.save'); 

                Route::get('/plans/edit/{id}', [SubscriptionController::class,'editPlan'])->name('plans.edit');
                Route::post('/plans/update', [SubscriptionController::class,'updatePlan'] )->name('plans.update'); 
            });

        });

        //------ When user not active or not part of current system ----------
        Route::group(['middleware' => ['auth:sanctum', 'verified']], function ($subdomain) {
            Route::any('/deactive', function() { return view('deactive'); }); 
        });
    });    
   
});


// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');
