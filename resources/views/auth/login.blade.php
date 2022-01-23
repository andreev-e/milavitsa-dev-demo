@extends('layouts.app')
@section('content')
	<div class="container h-100">
		<div class="row  h-100 justify-content-center align-items-center">
			<div class="col-lg-4 col-12">
				<div class="text-center h2">DIT-RPA</div>
				{{--                <img src="/logo-red.png" class="img-fluid" alt="">--}}
				<form class="form-horizontal" role="form" method="POST" action="{{ url('/login') }}">
					{{ csrf_field() }}
					<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
						{{--                        <label for="email" class="col-md-4 control-label">E-mail</label>--}}
						<input id="email" type="email" class="form-control field form-trim1" data-field="1" name="email" value="{{ old('email') }}" placeholder="E-mail">
						@if ($errors->has('email'))
							<span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
						@endif
					</div>
					<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
						{{--                        <label for="password" class="col-md-4 control-label">Пароль</label>--}}
						<input id="password" type="password" class="form-control field form-trim2" data-field="2" name="password" placeholder="Пароль">
						@if ($errors->has('password'))
							<span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
						@endif
					</div>
					{{--<div class="form-group">--}}
					{{--<div class="col-md-6 col-md-offset-4">--}}
					{{--<div class="checkbox">--}}
					{{--<label>--}}
					{{--<input type="checkbox" name="remember"> Remember Me--}}
					{{--</label>--}}
					{{--</div>--}}
					{{--</div>--}}
					{{--</div>--}}
					<div class="form-group text-center">
						<button type="submit" class="btn btn-primary">
							<i class="fa fa-btn fa-sign-in"></i> Войти
						</button>
						{{--<a class="btn btn-link" href="{{ url('/password/reset') }}">Forgot Your Password?</a>--}}
					</div>
				</form>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function () {
			$('.field').keyup(function () {
				$(this).val($(this).val().trim());
			})
		})
	</script>
@endsection
