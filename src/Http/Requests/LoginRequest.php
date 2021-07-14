<?php

namespace Botble\Comment\Http\Requests;

use Botble\Support\Http\Requests\Request;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ];
    }
}
