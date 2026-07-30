<!doctype html>
<html>
<head><meta charset="utf-8"><title>Register</title></head>
<body>
    <form method="POST" action="{{ route('auth-kit.register.attempt') }}">
        @csrf
        @foreach (['name', 'email', 'password'] as $f)
            @error($f)<p role="alert">{{ $message }}</p>@enderror
        @endforeach
        <label>Name <input type="text" name="name" value="{{ old('name') }}" required></label>
        <label>Email <input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password <input type="password" name="password" required></label>
        <label>Confirm <input type="password" name="password_confirmation" required></label>
        <button type="submit">Register</button>
    </form>
</body>
</html>
