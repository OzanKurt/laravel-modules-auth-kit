<!doctype html>
<html>
<head><meta charset="utf-8"><title>Forgot password</title></head>
<body>
    @if (session('status'))<p role="status">{{ session('status') }}</p>@endif
    <form method="POST" action="{{ route('auth-kit.password.email') }}">
        @csrf
        @error('email')<p role="alert">{{ $message }}</p>@enderror
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <button type="submit">Email password reset link</button>
    </form>
</body>
</html>
