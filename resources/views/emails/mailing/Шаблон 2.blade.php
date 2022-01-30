@component('mail::message')
# {{ $subj }}

{{ $text }}

@component('mail::button', ['url' => ''])
Кнопочка
@endcomponent

Спасибо за внимание,<br>
{{ config('app.name') }}
<!-- Шаблон Шаблон 2 -->
@endcomponent
