@extends('layouts.app')

@section('title', 'Sign in · GD Login')

@section('content')
<header class="hero">
    <div>
        <p class="eyebrow">Welcome back</p>
        <h1>Access your workspace</h1>
        <p class="lead">Sign in to reach your personalized dashboard.</p>
    </div>
</header>

<div class="grid">
    <section class="card">
        <h2>Sign in</h2>
        @if (session('status'))
            <div class="banner success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="banner error">
                <ul style="margin:0;padding-left:1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <label>
                <span>Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label>
                <span>Password</span>
                <input type="password" name="password" required>
            </label>
            <label style="display:flex;align-items:center;gap:0.5rem;">
                <input type="checkbox" name="remember" value="1" style="width:auto;"> Remember me
            </label>
            <button type="submit">Sign in</button>
            <p class="hint">Need an account? <a class="link" href="{{ route('register') }}">Create one</a>.</p>
        </form>
    </section>
</div>
@endsection
