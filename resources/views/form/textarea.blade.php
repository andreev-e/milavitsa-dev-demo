<div class="form-group">
	<div class="row align-items-center">
		<div class="col-3 font-weight-bold text-right">
			{{ Form::label($name, $label) }}
		</div>
		<div class="col-9">
			{{ Form::textarea($name, (isset($value)?$value:request($name, NULL)) , ['class' => 'form-control','rows'=>3]) }}
			@if($errors->first($name))
				<div class="alert alert-danger">
					{{ $errors->first($name) }}
				</div>
			@endif
		</div>
	</div>
</div>
