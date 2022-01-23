<?php
/**
 * Updated by Leestarch at 6/30/20 11:09 PM
 */

/**
 * Updated by Leestarch at 6/30/20 5:09 AM
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
	/**
	 * Indicates whether the XSRF-TOKEN cookie should be set on the response.
	 * @var bool
	 */
	protected $addHttpCookie = TRUE;
	/**
	 * The URIs that should be excluded from CSRF verification.
	 * @var array
	 */
	protected $except = [
    ];
}
