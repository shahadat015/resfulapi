@component('mail::message')
# Hello {{$user->name}}

Thank you for create an account. Please verify your email using this button:

@php
	$url = route('verify', $user->verification_token);
@endphp

@component('mail::button', ['url' => $url])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
