<div class="form-group">
	<div class="row align-items-center">
		<div class="col-12 col-lg-3 font-weight-bold text-lg-right">
			{{ Form::label($name, $label) }}
		</div>
		<div class="col-12 col-md-9">
			{{Form::select($name, $options, request($name), ['class'=>'custom-select '])}}
		</div>
	</div>
</div>
