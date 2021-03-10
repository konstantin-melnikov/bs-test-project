@extends('layouts.base')

@section('title', 'Login page')

@section('content')
    <div class="card m-auto" style="max-width: 24rem;">
        <div class="card-header">Login to have access to currency rates</div>
        <div class="card-body">
            @error('login')
                <x-alert type="error" :message="$message"/>
            @enderror
            @error('info')
                <x-alert type="info" :message="$message"/>
            @enderror
            <form method="POST" action="{!! route('auth') !!}">
                @csrf
                <input
                    type="email"
                    name="email"
                    required
                    class="form-control mb-3"
                    value="{{ old('email') }}"
                    placeholder="Your Email"
                >
                <input
                    type="password"
                    name="password"
                    required
                    class="form-control mb-3"
                    placeholder="Password"
                >
                <button type="submit" class="btn btn-primary mb-3 w-100">Login</button>
        </form>
        </div>
    </div>
@endsection
