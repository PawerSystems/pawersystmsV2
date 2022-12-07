@extends('layouts.web')

@section('content')
<section class="page-section" id="bod1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-headingbooking text-uppercase">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Keep booking</font></font>
                </h2>
                <h3 class="section-subheadingbooking text-muted">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Note: This form is filled in automatically if you use your phone number and the system recognizes your number!</font></font>
                </h3>
            </div>
        </div>

        <form class="form-horizontal col-sm-10 col-sm-offset-0" id="contact-form" name="clientForm" action="/ps/tilmeld.holdtid" method="post" style="display: contents;" novalidate="novalidate">
            <div class="container container-center">
                <div class="row">
                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Mobile number:</font></font>
                        </label>
                        <div class="em-rem">
                            <input type="text" name="mobile_phone" id="mp" class="write" required="" placeholder="" />
                        </div>
                    </div>

                    <input type="hidden" name="CPRNR" value="0000000000" />
                    <input type="hidden" name="mednr" value="0000000000" />

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Name:</font></font>
                        </label>
                        <div class="em-rem">
                            <input type="text" id="minmaxlength" name="name" placeholder="" />
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Code:</font></font>
                        </label>
                        <div class="em-rem">
                            <input type="password" id="password" name="bookingcode" required="" placeholder="" />
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Email:</font></font>
                        </label>
                        <div class="em-rem">
                            <input type="email" id="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2, 4}$" required="" placeholder="" />
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Bringing a guest:</font></font>
                        </label>
                        <div class="em-rem">
                            <select id="guest" class="yndd" name="guest">
                                <option value="0">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">No</font></font>
                                </option>
                                <option value="1">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Yes</font></font>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Email reminder:</font></font>
                        </label>
                        <div class="em-rem">
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

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Time:</font></font>
                        </label>
                        <div class="em-rem em-rem22">
                            <select name="tid" id="tid-info" class="yndd">
                                <option value="" selected="">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Choose time</font></font>
                                </option>
                                <option value="09-11-2020 09:00+1537">
                                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">09-11-2020 09:00 Test event for bilal</font></font>
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12 col-md-6-mar">
                        <label class="labelbooking">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Comment:</font></font>
                        </label>
                        <div class="em-rem em-rem22">
                            <textarea id="com" name="com" maxlength="150" placeholder=""></textarea>
                        </div>
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
        </form>
    </div>
</section>

@stop