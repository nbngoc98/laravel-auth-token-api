<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SigupRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\ApiController;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Auth\NoAuthEmailVerificationRequest;

class AuthController extends ApiController
{

    /**
     * Get the broker to be used during password reset.
     *
     * @return PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /*
	 * Register new user
	*/
    public function signup(SigupRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = User::create($validated);
            $user->sendEmailVerificationNotification();
            $response['data'] = $user;
            DB::commit();
            return $this->successResponse($user, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Verify Email
     */
    public function verifyEmail(NoAuthEmailVerificationRequest $request)
    {
        try {
            $request->fulfill();
            return $this->successResponse($request->all());
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse($e->getMessage());
        }
    }

    /*
	 * Generate sanctum token on successful login
	*/
    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();

            $user = User::where('email', $validated['email'])->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                return $this->errorResponse("The provided credentials are incorrect.", Response::HTTP_BAD_REQUEST);
            }

            $data = [
                'access_token' => $user->createToken($request->email)->plainTextToken,
                'user' => $user,
            ];
            return $this->successResponse($data);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /*
	 * Get authenticated user details
	*/
    public function getAuthenticatedUser(Request $request)
    {
        return $this->successResponse($request->user());
    }

    /*
	 * Revoke token; only remove token that is used to perform logout (i.e. will not revoke all tokens)
	*/
    public function logout(Request $request)
    {

        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();
        //$request->user->tokens()->delete(); // use this to revoke all tokens (logout from all devices)
        return $this->successResponse(null);
    }

    public function sendPasswordResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse("Unprocessable Content", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $data = [
                'message' => 'Password reset request sent', 
                'email' => $request->email
            ];
            return $this->successResponse($data, Response::HTTP_OK, 'MSI00006');
        } else {
            return $this->errorResponse("Bad Request", Response::HTTP_BAD_REQUEST);
        }
    }

    public function verifyTokenUser($token, Request $request)
    {
        try {
            $credentials = $request->only('email');

            $user = $this->broker()->getUser($credentials);

            if (is_null($user)) {
                return $this->errorResponse("Invalid user", Response::HTTP_OK);
            }

            if (!$this->broker()->tokenExists($user, $token)) {
                return $this->errorResponse("Invalid token", Response::HTTP_OK);
            }

            return $this->successResponse("Verify success", Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return $this->errorResponse("Unprocessable Content", Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return $this->successResponse("Reset password success", Response::HTTP_OK);
        } else {
            return $this->errorResponse("Bad Request", Response::HTTP_BAD_REQUEST);
        }
    }
}
