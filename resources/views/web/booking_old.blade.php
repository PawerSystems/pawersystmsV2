@extends('layouts.web')

@section('content')


<section class="page-section" id="bod1">
    <div class="container">
        <div class="">
            <div class="col-lg-12 text-center">
                <h2 class="section-headingbooking text-uppercase">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Book consultation</font></font>
                </h2>
                <h3 class="section-subheadingbooking text-muted">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Note: This form is filled in automatically if you use your phone number and the system recognizes your number!</font></font>
                </h3>
            </div>
        </div>

        <form class="form-horizontal col-sm-10 col-sm-offset-0" id="contact-form" name="clientForm" method="post" novalidate="novalidate">
            <div class="container container-center">
                <div class="">
                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Mobile number:</font></font>
                            </label>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <input type="text" id="mp" name="mobile_phone" class="write" required="" placeholder="" />
                        </div>
                    </div>

                    <input type="hidden" name="CPRNR" value="0000000000" />
                    <input type="hidden" name="mednr" value="0000000000" />

                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Name:</font></font>
                            </label>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <input type="text" id="minmaxlength" name="name" placeholder="" />
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Code:</font></font>
                            </label>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <input type="password" id="password" name="bookingcode" required="" placeholder="" />
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Email:</font></font>
                            </label>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <input type="email" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2, 4}$" required="" placeholder="" />
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Email reminder:</font></font>
                            </label>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <select id="emailnotice" class="yndd" name="emailnotice">
                                <option value="Ja">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Yes</font></font>
                                </option>
                                <option value="Nej">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">No</font></font>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Time:</font></font>
                            </label>
                        </div>

                        <div class="col-md-9 col-sm-12 em-rem22">
                            <select name="tid" id="tid-info" class="yndd">
                                <option value="" selected="">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Choose time</font></font>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar marginBottom">
                        <div class="col-md-3 col-sm-12">
                            <label class="labelbooking">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Comment:</font></font>
                            </label>
                        </div>
                        <div class="col-md-9 col-sm-12">
                            <textarea id="com" name="com" maxlength="150" style="width: 100%;" placeholder=""></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar">
                        <p class="gdpr-note">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">By booking, you also accept our rules for GDPR and consent to the above information being stored in the system. </font></font>
                            <a href="https://test.pawersystems.dk/gdpr.html" target="_blank">
                                <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Read our GDPR here</font></font>
                            </a>
                        </p>
                    </div>

                    <div class="col-md-12 col-mrgn">
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><input type="submit" class="button-booking" value="Book Now" /></font>
                        </font>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

@stop