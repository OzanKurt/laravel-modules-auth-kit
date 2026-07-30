<?php

declare(strict_types=1);

return [
    'failed' => 'These credentials do not match our records.',

    // Deliberately non-committal: the same line is returned whether or not the
    // address belongs to an account, so it cannot be used to enumerate users.
    'password_reset_sent' => 'If that email address exists in our system, we have sent a password reset link.',
    'password_reset_success' => 'Your password has been reset.',
];
