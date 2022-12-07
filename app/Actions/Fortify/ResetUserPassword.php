<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use App\Models\Business;
use App\Models\User;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function reset($user, array $input)
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();
        
        $oemail = $user->email;
        $bcheck = Business::find($user->business_id);
        if($bcheck->business_name != session('business_name')){
            $business = Business::where('business_name',session('business_name'))->first();
            $user = User::where('email',$user->email)->where('business_id',$business->id)->first();
        }

        if($user == 'null' || $user == NULL){
            \Log::channel('custom')->info("User with this email try to reset pass in wrong location",['email' => $oemail, 'location' => session('business_name')]);
            echo "<script>alert('User does not belog to this location');window.location.replace('/');</script>";
            exit();
        }
        else{
            \Log::channel('custom')->info("Password has been changed successfully!",['user_id' => $user->id, 'password' => $input['password']]);

            $user->forceFill([
                'password' => Hash::make($input['password']),
            ])->save();
        }
    }
}
