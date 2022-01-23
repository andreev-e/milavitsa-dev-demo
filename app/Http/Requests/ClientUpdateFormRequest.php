<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateFormRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 * @return bool
	 */
	public function authorize()
	{
		return TRUE;
	}

	/**
	 * Get the validation rules that apply to the request.
	 * @return array
	 */
	public function rules()
	{
		$data = [
			'phone' => 'required',
			'email' => 'required',
		];
		if (request('phone') or request('email'))
			$data = [];
		return [
			'i' => 'required',
			$data,
		];
	}
}
