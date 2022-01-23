<div class="form-group">
	<div class="row align-items-center">
		<div class="col-12 col-lg-3 font-weight-bold text-lg-right">
			{{ Form::label($name, $label) }}
		</div>
		
		<div class="col-12 col-md-9">
			{{ Form::file($name.'[]', array('class' => 'form-control-file '.(isset($class)?$class:''), 'multiple' => TRUE)) }}
			@if($errors->first($name))
				<div class="alert alert-danger">
					{{ $errors->first($name) }}
				</div>
			@endif
		</div>
	</div>
</div>
