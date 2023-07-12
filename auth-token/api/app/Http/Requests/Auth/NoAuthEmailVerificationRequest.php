<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Http\Requests\BaseFormRequest;

/** Code taken from Illuminate\Foundation\Auth\EmailVerificationRequest */
class NoAuthEmailVerificationRequest extends BaseFormRequest
{
    public User $user;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->user = User::findOrFail($this->route('id'));

        if (! hash_equals((string) $this->route('id'), (string) $this->user->getKey())) {
            return false;
        }

        if (! hash_equals((string) $this->route('hash'), sha1($this->user->getEmailForVerification()))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public function fulfill(): bool
    {
        if (! $this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();
        }

        return true;
    }

    public function withValidator($validator)
    {
        return $validator;
    }
}
