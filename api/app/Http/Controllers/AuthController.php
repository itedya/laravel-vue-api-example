<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
	public function login() {
		$validator = Validator::make([
			'username' => ['required', 'string', 'min:3', 'max:64'],
			'password' => ['required', 'string', 'min:8', 'max:64']
		]);

		if ($validator->fails()) {
			throw ValidationException::withMessages($validator->errors());
		}

		$data = $validator->validated();

		$user = User::where('username', $data['username'])->first();

		if ($user === null) {
			throw ValidationException::withMessages([
				'data' => [
					'errors' => [
						'username' => ['Podana nazwa użytkownika jest nieprawidłowa']
					]
				]
			]);
		}

		if (!Hash::check($data['password'], $user->password)) {
			throw ValidationException::withMessages([
				'data' => [
					'errors' => [
						'username' => ['Podana nazwa użytkownika jest nieprawidłowa']
					]
				]
			]);
		}

		$token = Crypt::encryptString("token_for_id:" . $user->id);

		return [
			'token' => $token,
			'user' => $user
		];
	}
}

