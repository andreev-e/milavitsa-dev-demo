@php
	if (!empty($value)) {
		$selected = json_decode($value);
	} else {
		$selected = [];
	}
@endphp
@foreach ($options as $value => $text)
	<div class="py-3 form-check" style="border-top: 1px solid #333;">
		<input type="checkbox" id="{{ $name }}-{{$value}}" name="{{ $name }}[]" value="{{ $value }}" @if (in_array($value, $selected)) checked @endif class="form-check-input">
		<label for="{{ $name }}-{{$value}}" class="form-check-label">{{ $text }}</label>
	</div>
@endforeach
