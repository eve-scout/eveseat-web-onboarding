<?php

/*
 * This file is part of Onboarding SeAT Add-on.
 *
 * Copyright (c) 2016 Johnny Splunk <johnnysplunk@eve-scout.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EveScout\Seat\Web\Onboarding\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Seat\Web\Models\User;
use Mail;

/**
 * Class RegisterController
 * @package EveScout\Seat\Web\Onboarding\Http\Controllers\Auth
 */
class RegisterController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new authentication controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|max:255|unique:users',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User;
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->confirmation_token = bin2hex(Str::randomBytes(32));

        if ($user->save()) {

            $this->sendConfirmation($user);

            return $user;
        }
    }

    protected function sendConfirmation(User $user)
    {
        // Prepare data to be sent along with the email. These
        // are accessed by their keys in the email template
        $data = array(
            'confirmation_token' => $user->confirmation_token
        );

        // Send the email with the activation link
        Mail::send('eveseat-onboarding::emails.auth.register', $data, function ($message) use ($user) {

            $message->to($user->email, 'EVE-Scout Auth')
                    ->subject('EVE-Scout Auth Account Activation Required');
        });
    }
}
