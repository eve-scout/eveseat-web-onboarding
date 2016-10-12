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

use Seat\Web\Models\User;
use Auth;

/**
 * Class ConfirmController
 * @package EveScout\Seat\Web\Onboarding\Http\Controllers\Auth
 */
class ConfirmController extends Controller
{
    public function getConfirm($confirmation_token)
    {
        $user = User::where('confirmation_token', $confirmation_token)->first();

        if ($user) {
            $user->confirmation_token = '';
            $user->active = TRUE;

            $user->save();

            Auth::login($user);

            return redirect()->route('home')
                ->with('success', 'Your email was confirmed and account activated. Thanks!');
        }

        return redirect()->route('auth.login')
            ->with('error', 'Invalid or expired email confirmation. If you believe this is an error contact administrator.');
    }
}
