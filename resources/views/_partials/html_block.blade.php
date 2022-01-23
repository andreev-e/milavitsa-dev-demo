<div class="{{$block->position??''}}
	pb-{{$block->pb??''}}
	pt-{{$block->pt??''}}
	pl-{{$block->pl??''}}
	pr-{{$block->pr??''}}
	col-md-{{$block->cols ?? 12}}">
	@switch($block->type)
		@case('photo')
		<img src="/{{$block->value}}" class="img-fluid" alt="about image">
		@break
		@case('video')
		<video src="/{{$block->value}}" controls class="img-fluid"></video>
		@break
		@case('text')
		<p>{!! $block->value !!}</p>
		@break
	@endswitch
</div>
