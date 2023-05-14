<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AuthenticatedByToken
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
	 */
	public function handle(Request $request, Closure $next): Response
	{
		try {
			$header = $request->header('Authorization');

			if ($header === null) {
				abort(401);
			}

			$splittedHeader = explode($header, " ");

			if (count($splittedHeader) < 2) {
				abort(401);
			}

			if ($splittedHeader[0] !== "Bearer") {
				abort(401);
			}

			$decrypted = Crypt::decryptString($splittedHeader[1]);

			if (!str_contains($decrypted, "token_for_id:")) {
				abort(401);
			}

			$id = intval(str_replace("token_for_id:", "", $decrypted));

			$user = User::find($id);

			if ($user === null) {
				abort(401);
			}

			auth()->loginAs($user);
		} catch (\Exception $e) {
			Log::error($e);	
			abort(401);
		}

		return $next($request);
	}
}
