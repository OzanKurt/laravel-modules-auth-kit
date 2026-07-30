<!doctype html>
<html>
<head><meta charset="utf-8"><title>Verify your email</title></head>
<body>
    <p>Please verify your email address by clicking the link we just emailed you.</p>
    @if (session('status') === 'verification-link-sent')
        <p role="status">A new verification link has been sent.</p>
    @endif
    <form method="POST" action="{{ route('auth-kit.verification.send') }}">
        @csrf
        <button type="submit">Resend verification email</button>
    </form>
</body>
</html>
