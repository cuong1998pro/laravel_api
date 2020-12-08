@component('mail::message')
# Hello {{$user->name}}

You have changed your email, so we need to verify your new email. Please verify your email using this link:

@component('mail::button', ['url' =>  route('verify' , $user->verification_token)])
Verify Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
