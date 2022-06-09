<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $reture = [
            'username' => 'required|min:6|max:12|unique:admin_user,username,' . $this->get('id') . ',id',
        ];
        if ($this->get('password') || $this->get('password_confirmation')) {
            $reture['password'] = 'required|confirmed|min:6|max:14';
        }
        return $reture;
    }
}
