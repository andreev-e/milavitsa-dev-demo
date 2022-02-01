@component('mail::message')
# {{ $subj }}

{{ $text }}

@component('mail::button', ['url' => ''])
Кнопочка
@endcomponent

Спасибо за внимание,<br>
{{ config('app.name') }}
<img src="{{ config('app.url') }}/pixel/{{ $pixel }}" />
<!-- Шаблон Шаблон 2 -->
@endcomponent
