<?php

/*
 * This file is part of Onboarding SeAT Add-on.
 *
 * Copyright (c) 2016 Johnny Splunk <johnnysplunk@eve-scout.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace EveScout\Seat\Web\Onboarding\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Pheal\Pheal;
use Seat\Eveapi\Helpers\JobContainer;
use Seat\Eveapi\Jobs\CheckAndQueueKey;
use Seat\Eveapi\Models\Eve\ApiKey as ApiKeyModel;
use Seat\Eveapi\Models\JobTracking;
use Seat\Eveapi\Traits\JobManager;
use Seat\Web\Models\User;
use Seat\Web\Validation\ApiKey;

/**
 * Class KeyController
 * @package Seat\Web\Http\Controllers\Api
 */
class KeyController extends Controller
{

    use JobManager;

    /**
     * @param \Seat\Web\Validation\ApiKey $request
     *
     * @return \Illuminate\View\View|string
     */
    public function checkKey(ApiKey $request)
    {

        $key_id = $request->input('key_id');
        $v_code = $request->input('v_code');

        $legacy_key = false;

        try {

            // Pheal does not have getters/setters for
            // the keyid/vcode, sadly. So, we cant DI it.
            $result = (new Pheal($key_id, $v_code))
                ->accountScope->APIKeyInfo();

            $key_type = $result->key->type;
            $access_mask = $result->key->accessMask;
            $characters = $result->key->characters;

        } catch (\Exception $e) {

            return response()->json([
                'error' => ['Unable to retrieve API Key. Please check that you entered a valid Key ID and Verification Code and try again.<br><br>' . $e->getMessage()]
            ])->setStatusCode(422, 'Unprocessable Entity');

        }

        $key = ApiKeyModel::where('key_id', $key_id)->first();

        if ($key)
            $legacy_key = true;

        $access_map = ($key_type == 'Corporation' ?
            config('eveapi.access_bits.corp') : config('eveapi.access_bits.char'));

        return view('eveseat-onboarding::api.ajax.result',
            compact('key_type', 'access_mask', 'characters',
                'access_map', 'key_id', 'v_code', 'legacy_key'));
    }

    /**
     * @param \Seat\Web\Validation\ApiKey       $request
     * @param \Seat\Eveapi\Helpers\JobContainer $job
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addKey(ApiKey $request, JobContainer $job)
    {

        $key_id = $request->input('key_id');
        $v_code = $request->input('v_code');

        // First validate access_mask
        try {

            // Pheal does not have getters/setters for
            // the keyid/vcode, sadly. So, we cant DI it.
            $result = (new Pheal($key_id, $v_code))
                ->accountScope->APIKeyInfo();

            $key_type = $result->key->type;
            $access_mask = $result->key->accessMask;
            $characters = $result->key->characters;

        } catch (\Exception $e) {

            return redirect()->route('api.key')
                ->with('error', $e->getMessage());

        }

        // Check for minimum access_mask
        if ($key_type != 'Corporation' && setting('force_min_mask', true) == 'yes' && $access_mask < setting('min_access_mask', true)) {

            // Is there a legacy key active?
            $key = ApiKeyModel::where('key_id', $key_id)->first();

            if (! $key)
                return redirect()->route('api.key')
                    ->with('error', trans('web::seat.insufficient_access_mask'));

        }

        // Get or create the API Key
        $api_key = ApiKeyModel::firstOrNew([
            'key_id' => $key_id,
        ]);

        // Set the current user as the owner of the key
        // and enable it.
        $api_key->fill([
            'v_code'  => $v_code,
            'user_id' => auth()->user()->id,
            'enabled' => true,
        ]);

        $api_key->save();

        // For *some* reason, key_id is 0 here after the
        // fill() and save(). So, set it again so that
        // the update job wont fail to give Pheal a
        // key_id from the model.
        $api_key->key_id = $key_id;

        // Prepare the JobContainer for the update job
        $job->scope = 'Key';
        $job->api = 'Scheduler';
        $job->owner_id = $api_key->key_id;
        $job->eve_api_key = $api_key;

        // Queue the update Job
        $job_id = $this->addUniqueJob(
            CheckAndQueueKey::class, $job);

        return redirect()->route('api.key')
            ->with('success', trans('web::seat.add_success',
                ['jobid' => $job_id]));

    }
}
