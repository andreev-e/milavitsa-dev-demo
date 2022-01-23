<div class="rounded bg-light border p-3">
@if(isset($model))
	{{ Form::model($model, ['class' => 'form w-100', 'files' => isset($files)?$files:NULL,]) }}
@else
	{{ Form::open(['method' => isset($method)?$method:'post'], ['class' => 'form w-100', 'files' => isset($files)?$files:NULL,]) }}
@endif
