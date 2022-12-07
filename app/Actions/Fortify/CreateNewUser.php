<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Business;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        $bus = \DB::table('businesses')->select('id')->Where('business_name','=',$input['business_name'])->first();
        if($bus){
            $already = \DB::table('users')->select()->Where('email','=',$input['email'])->Where('business_id','=',$bus->id)->get()->count();
            if(!$already){
                \Log::channel('custom')->info("New user created!",['business_id' => $bus->id, 'user_email' => $input['email'], 'user_name' => $input['name'], 'user_role' => $input['role']]);

                return User::create([
                    'name' => $input['name'],
                    'business_id' => $bus->id,
                    'role' => $input['role'],
                    'access' => '-1', // For now it's all
                    'email' => $input['email'],
                    'password' => Hash::make($input['password']),
                ]);
            }
            else{
                \Log::channel('custom')->warning("User with same email already exist in this system.",['business_id' => $bus->id, 'user_email' => $input['email'], 'user_name' => $input['name'], 'user_role' => $input['role']]);

                echo "User already exist!";
                header("Refresh:0");
                exit();
            }
        }else{
            $business = Business::create([
                'business_name' => $input['business_name'],
                'languages' => '-1',    // For now it's all
                'access_modules' => '-1', // For now it's all
                'time_interval' => 5,
            ]);
            \Log::channel('custom')->info("New business/location created.",['business_name' => $input['business_name']]);

            \Log::channel('custom')->info("Super admin created of business.",['business_id' => $business->id, 'user_name' => $input['name'], 'email' => $input['email']]);

            return User::create([
                'name' => $input['name'],
                'business_id' => $business->id,
                'role' => $input['role'],
                'access' => '-1', // For now it's all
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);
        }
        
    }
}
