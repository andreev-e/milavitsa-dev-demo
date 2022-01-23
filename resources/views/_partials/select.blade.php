@php
	$multi = isset($multi) ? $multi : null;
	if (!empty($value)) {
		if ($multi) {
			$selected = json_decode($value);
		} else {
			$selected = [$value];
		}
	} else {
		$selected = [];
	}
@endphp
<select name="{{ $name }}" id="{{ $name }}" @if ($multi) multiple size="7" @endif class="form-control">
	<option value="">Не выбрано</option>
	@foreach ($options as $value => $text)
		<option value="{{ $value }}" @if (in_array($value, $selected)) selected @endif>{{ $text }}</option>
	@endforeach
</select>
