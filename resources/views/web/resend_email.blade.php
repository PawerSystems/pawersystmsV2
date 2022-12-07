
@extends('layouts.web')

@section('content')


<section class="page-section" id="about">
    <div class="container">
        <div class="row">
            <div class="col-lg-14 text-center">
                <h2 class="section-heading text-uppercase">
                    <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Resend email with booking</font></font>
                </h2>
                <p>
                    <font style="vertical-align: inherit;">
                        <font style="vertical-align: inherit;">Use the email address that was used when booking. </font>
                        <font style="vertical-align: inherit;">The system will then automatically send a new email with the bookings made with it.</font>
                    </font>
                </p>

                <form method="POST">
                    <div class="form-group">
                        <label class="control-label col-sm-14">
                            <font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Email:</font></font>
                        </label>
                        <div class="col-sm-14">
                            <input type="text" class="form-control" name="email1" id="sn" />
                        </div>
                    </div>
                    <div class="col-sm-14 col-sm-offset-1">
                        <br />
                        <font style="vertical-align: inherit;">
                            <font style="vertical-align: inherit;"><input type="submit" class="button-booking" value="Resend email" /></font>
                        </font>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@stop