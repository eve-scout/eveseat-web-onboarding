@extends('eveseat-onboarding::emails.layouts.email')

@section('content')

<p>
Activate your account by following this link:<br>
<a href="{{ route('auth.register.confirm', ['confirmation_token' => $confirmation_token]) }}">{{ route('auth.register.confirm', ['confirmation_token' => $confirmation_token]) }}</a>
</p>

@stop