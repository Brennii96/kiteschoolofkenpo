<?php

namespace App\Http\Requests\Auth;

use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;

class MemberEmailVerificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $member = Member::findOrFail($this->route('id'));

        if (! hash_equals(
            (string) $this->route('hash'),
            sha1($member->getEmailForVerification())
        )) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * Get the member from the route parameter.
     */
    public function member(): Member
    {
        return Member::findOrFail($this->route('id'));
    }
}
