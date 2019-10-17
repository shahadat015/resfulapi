@component('mail::message')
# Hello {{$user->name}}

You changed your email, we need to verify this new address. Please use this button below:

@php
	$url = route('verify', $user->verification_token);
@endphp

@component('mail::button', ['url' => $url])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
