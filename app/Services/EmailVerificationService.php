<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

class EmailVerificationService
{
    /**
     * Generate a 4-character lowercase alphanumeric code and set expiry.
     */
    public function generateCode(User $user, int $ttlMinutes = 15): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        $user->verification_code = $code;
        $user->verification_code_expires_at = now()->addMinutes($ttlMinutes);
        $user->save();

        return $code;
    }

    /**
     * Send the verification code to the user's email.
     */
    public function sendCode(User $user): void
    {
        if (!$user->verification_code) {
            $this->generateCode($user);
        }

        $code = $user->verification_code;
        $to = $user->email;

        // 主旨為「驗證碼」，內容僅代碼本體
        Mail::raw($code, function ($message) use ($to) {
            $message->to($to)->subject('驗證碼');
        });
    }

    /**
     * Verify the provided code; returns true on success.
     */
    public function verifyCode(User $user, string $code): bool
    {
        if (!$user->verification_code) {
            return false;
        }

        if (!$user->verification_code_expires_at || now()->greaterThan($user->verification_code_expires_at)) {
            return false;
        }

        if (strtolower(trim($code)) !== strtolower($user->verification_code)) {
            return false;
        }

        // Success: mark verified and clear code
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        return true;
    }
}
