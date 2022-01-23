<div class="form-check form-check-inline">
	{{ Form::radio($name, $value, $checked, ['class' => 'form-check-input '.(isset($class)?$class:''),'id' => $value]) }}
	{{ Form::label($value, $label, ['class' =>'form-check-label']) }}
	@if($errors->first($name))
		<div class="alert alert-danger">
			{{ $errors->first($name) }}
		</div>
	@endif
</div>
