<!doctype html>
<html>
<head><meta charset="utf-8"><title>Login</title></head>
<body>
    <form method="POST" action="{{ route('auth-kit.login.attempt') }}">
        @csrf
        @error('email')<p role="alert">{{ $message }}</p>@enderror
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        @if (config('auth-kit.login.allow_remember'))
            <label><input type="checkbox" name="remember" value="1"> Remember me</label>
        @endif
        <button type="submit">Log in</button>
    </form>
</body>
</html>
