<!doctype html>
<html>
<head><meta charset="utf-8"><title>Reset password</title></head>
<body>
    <form method="POST" action="{{ route('auth-kit.password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        @foreach (['email', 'password'] as $f)
            @error($f)<p role="alert">{{ $message }}</p>@enderror
        @endforeach
        <label>Email <input type="email" name="email" value="{{ old('email', $email) }}" required></label>
        <label>New password <input type="password" name="password" required></label>
        <label>Confirm <input type="password" name="password_confirmation" required></label>
        <button type="submit">Reset password</button>
    </form>
</body>
</html>
