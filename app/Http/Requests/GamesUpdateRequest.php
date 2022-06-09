<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GamesUpdateRequest extends FormRequest
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
            'game_name' => 'required|min:1',
            'start_time' => 'required|min:1',
            //'days' => 'integer|between:-900,900',
        ];
        return $reture;
    }
}
