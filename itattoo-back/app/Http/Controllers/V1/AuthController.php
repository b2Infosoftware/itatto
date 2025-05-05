<?php

namespace App\Http\Controllers\V1;

use App\Models\Log;
use App\Helpers\Logs;
use App\Models\Staff;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Resources\V1\StaffResource;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Resources\V1\CustomerResource;
use App\Http\Requests\V1\ResetPasswordRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registers a user
     *
     * @param RegisterRequest $request
     * @return void
     */
    public function register(RegisterRequest $request)
    {
        $staff = Staff::create($request->except([
            'password_confirmation',
            'tz',
            'organisation_name',
            'country_id',
            'accept_privacy_terms' => $request->accept_privacy_terms,
            'accept_terms_conditions' => $request->accept_terms_conditions,
        ]));

        Organisation::create([
            'name' => $request->organisation_name,
            'slug' => Str::slug($request->organisation_name),
            'owner_id' => $staff->id,
            'country_id' => $request->country_id,
            'timezone' => $request->tz,
        ]);

        $token = $staff->createToken('loginToken')->plainTextToken;

        // event(new StaffCreated($staff));
        $staff->sendEmailVerificationNotification();

        return response()->json(['accessToken' => $token], 200);
    }

    public function loginGoogleRedirect(Request $request)
    {
        $request->validate([
            'status' => 'required|in:login,register',
            'login_as' => $request->status === 'register' ? 'nullable' : 'required|in:staff,customer',
        ]);

        $state = base64_encode(json_encode([
            'login_as' => $request->login_as,
            'status' => $request->status,
            'redirect' => $request->redirect,
        ]));

        return response()->json([
            'redirect_url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl() . '&state=' . $state,
        ]);
    }


    public function googleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $state = json_decode(base64_decode($request->input('state', '')), true);
        $status = $state['status'] ?? 'login';
        $redirect_url = $state['redirect'] ?? env('FRONTEND_URL', 'https://staging.itattoo.com') . '/auth/callback?status=' . $status;
    
        $user = Staff::whereEmail($googleUser->getEmail())->first();
    
        if (!$user && $status === 'register') {
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
    
            $user = Staff::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'accept_privacy_terms' => 1,
                'accept_terms_conditions' => 1,
            ]);
    
            $orgName = trim("$firstName $lastName") ?: 'New Organisation';
            $orgSlug = Str::slug($orgName);
    
            Organisation::create([
                'name' => $orgName,
                'slug' => $orgSlug,
                'owner_id' => $user->id,
            ]);
        }
    
        if (!$user) {
            $status = 'register';
        
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
        
            $user = Staff::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'email_verified_at' => now(),
                'accept_privacy_terms' => 1,
                'accept_terms_conditions' => 1,
            ]);
        
            $orgName = trim("$firstName $lastName") ?: 'New Organisation';
            $orgSlug = Str::slug($orgName);
        
            Organisation::create([
                'name' => $orgName,
                'slug' => $orgSlug,
                'owner_id' => $user->id,
            ]);
        }
    
        $user->google_id = $googleUser->getId();
        $user->email_verified_at = now();
        $user->save();
    
        $token = $user->createToken('google-auth')->plainTextToken;
    
        return redirect()->to($redirect_url . '&token=' . $token);
    }    

    /**
     * Authenticates an user and generates a token
     *
     * @param LoginRequest $request
     * @return void
     */
    public function login(LoginRequest $request)
    {

        // return $request->all();
        if ($request->login_as === 'staff') {
            $staff = Staff::whereEmail($request->email)->first();
            if (! $staff) {
                return response()->json(['message' => trans('auth.bad_credentials')], 422);
            }

            Log::addLog('login', $staff);

            return $this->loginByUserType($staff, $request->all());
        }

        $customer = Customer::whereEmail($request->email)->first();
        if (! $customer) {
            return response()->json(['message' => trans('auth.bad_credentials')], 422);
        }

        Log::addLog('login', $customer);

        return $this->loginByUserType($customer, $request->all());
    }

    public function loginByUserType($user, $data)
    {
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => trans('auth.bad_credentials')], 422);
        }

        // generate login token for the user
        $token = $user->createToken('loginToken')->plainTextToken;

        return response()->json([
            'accessToken' => $token,
        ], 200);
    }

    /**
     * Returns the profile of an authenticated user.
     * Standard route for most APIs although it may seem useless.
     *
     * @param Request $request
     * @return void
     */
    public function me(Request $request)
    {
        $user = auth()->user();
        if (auth()->user() instanceof Staff) {
            $user->load('organisations', 'permissions', 'locations', 'role');
            if ($user->isSuperAdmin()) {
                $user->permissions = Permission::get();
            }

            return new StaffResource($user);
        }

        $user->load('organisations');

        return new CustomerResource($user);
    }

    /**
     * Logs out a user and delets its in-use token.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request)
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json(['message' => trans('auth.logged_out')], 204);
    }

    /** 
     * Handles a "Forgot Password" action.
     * User is being sent a reset-password link
     * via email, if the email exists in the database.
     *
     * @param Request $request
     * @return void
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker($request->for)->sendResetLink($request->only('email'));

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => trans($status),
            ]);
        }

        return response()->json([
            'message' => trans($status),
        ]);
    }

    /**
     * Resets the password for a user.
     * Also, removes all login-tokens which means the user is going to be
     * disconnected from all of their devices, excepting API generated tokens.
     *
     * @param ResetPasswordRequest $request
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::broker($request->for)->reset($request->except(['for']), function ($user) use ($request) {
            $user->update(['password' => $request->password]);
            $user->tokens()->where('name', 'loginToken')->delete();
        });

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => trans('passwords.reset'),
            ]);
        }

        return response()->json([
            'message' => trans($status),
        ], 403);
    }

    /**
     * Resends a new verification email to the logged-in user.
     *
     * @param Request $request
     * @return void
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => trans('auth.email_already_verified'),
            ], 422);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json([
            'message' => trans('auth.verification_email_sent'),
        ], 200);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verifyEmail(Request $request)
    {
        if (! $request->hasValidSignature()) {
            return response()->json([
                'message' => trans('auth.broken_verification_link'),
            ], 403);
        }

        if (request()->has('staff_id')) {
            $user = Staff::find($request->staff_id);
        } else {
            $user = Customer::find($request->customer_id);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => trans('auth.email_already_verified'),
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => trans('auth.email_verified_successfully'),
        ]);
    }

    public function changeDefaultLocation(Location $location)
    {
        $this->authorize('setDefault', $location);
        auth()->user()->update([
            'default_location_id' => $location->id,
            'default_organisation_id' => $location->organisation_id,
        ]);

        return response()->json([
            'message' => trans('general.refresh_the_page'),
        ]);
    }

    public function changeDefaultOrganisation(Organisation $organisation)
    {
        $user = auth()->user();
        if (! $user->isSuperAdmin() && ! $user->organisations()->where('organisations.id', $organisation->id)->exists()) {
            abort(403);
        }
        if ($user->isSuperAdmin()) {
            $locId = $organisation->locations()->first()->id;
        } else {
            $location = $user->locations()->whereOrganisationId($organisation->id)->first();
            $locId = $location?->id ?: null;
        }
        if (! $locId) {
            abort(403, 'You are not associated with a location within that organisation');
        }
        auth()->user()->update([
            'default_organisation_id' => $organisation->id,
            'default_location_id' => $locId,
            'role_id' => $user->organisations()->whereOrganisationId($organisation->id)->first()?->pivot?->role_id,
        ]);

        return response()->json([
            'message' => trans('general.refresh_the_page'),
        ]);
    }

    public function setStaffShortlist(Request $request)
    {
        auth()->user()->update([
            'staff_shortlist_ids' => $request->ids,
        ]);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.data')]),
        ]);
    }

    public function setPreferredStaff(Staff $staff)
    {
        auth()->user()->update([
            'preselected_staff_id' => $staff->id,
        ]);

        return response()->json([
            'message' => trans('general.update', ['resource' => trans('resource.data')]),
        ]);
    }
}
