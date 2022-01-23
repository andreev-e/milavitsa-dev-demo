<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
	/**
	 * The policy mappings for the application.
	 * @var array
	 */
	protected $policies = [
		'App\Model' => 'App\Policies\ModelPolicy',
	];

	/**
	 * Register any application authentication / authorization services.
	 * @param \Illuminate\Contracts\Auth\Access\Gate $gate
	 * @return void
	 */
	public function boot(GateContract $gate)
	{
		$this->registerPolicies($gate);
		Gate::define('access', function ($user, $data) {
			$access = $user->accesses
				->where('action', $data)
				->first();
			if ($access)
				return $access;
			else
				return FALSE;
		});
		/* todo удалить эти политики */
		Gate::define('admin', function ($user) {
			return in_array($user->type, [2]) ? TRUE : FALSE;
		});
		Gate::define('manager', function ($user) {
			return in_array($user->type, [2, 3]) ? TRUE : FALSE;
		});
		Gate::define('back', function ($user) {
			return in_array($user->type, [2, 3, 4]) ? TRUE : FALSE;
		});
		Gate::define('seller', function ($user) {
			return in_array($user->type, [1]) ? TRUE : FALSE;
		});
	}
}
