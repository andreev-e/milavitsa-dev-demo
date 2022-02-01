@component('mail::message')
# {{ $subj }}

{{ $text }}

@component('mail::button', ['url' => ''])
Кнопочка
@endcomponent

Спасибо за внимание,<br>
{{ config('app.name') }}
{{ config('app.url') }}/pixel/{{ $pixel }}
<img src="{{ config('app.url') }}/pixel/{{ $pixel }}" alt="p"/>
<!-- Шаблон default -->
@endcomponent
