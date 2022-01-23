@extends('layouts.app')
@section('content')
	<div class="form-group">

    </div>
	<script>
		$(document).ready(function () {
			$('.field').keyup(function () {
				$(this).val($(this).val().trim());
			})
		})
	</script>
@endsection
