<div class="form-group">
	<div class="row align-items-center">
		<div class="col-12 col-lg-3 font-weight-bold text-lg-right">
			{{ Form::label($name, $label) }}
		</div>
		<div class="col-12 col-md-9">
			{{ Form::text($name, isset($value)?$value:request($name, NULL), ['class' => 'form-control '.(isset($class)?$class:''),'placeholder' => (isset($placeholder)?$placeholder:''),'autocomplete'=>'off' ,(isset($disabled)?'disabled':'')]) }}
			@if($errors->first($name))
				<div class="alert alert-danger">
					{{ $errors->first($name) }}
				</div>
			@endif
		</div>
	</div>
</div>
