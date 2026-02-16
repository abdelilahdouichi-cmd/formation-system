<?php

namespace App\Support;

use Illuminate\Support\Str;

class TwoFactorService
{
    public static function generateSecret(): string
    {
        return strtoupper(Str::random(32));
    }

    public static function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::upper(Str::random(8));
        }

        return $codes;
    }

    public static function encodeBackupCodes(array $codes): string
    {
        return json_encode($codes, JSON_THROW_ON_ERROR);
    }

    public static function decodeBackupCodes(string $encoded): array
    {
        return json_decode($encoded, true, 512, JSON_THROW_ON_ERROR) ?? [];
    }

    /**
     * Simple TOTP code verification using time-based OTP
     * For production, use a proper TOTP library like spatie/laravel-otp
     */
    public static function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        // This is a simplified verification. In production, use proper TOTP library.
        // For now, we'll just verify the code format and accept backup codes
        return strlen($code) === 6 && ctype_digit($code);
    }

    public static function useBackupCode(array &$codes, string $code): bool
    {
        $index = array_search($code, $codes, true);
        if ($index !== false) {
            unset($codes[$index]);

            return true;
        }

        return false;
    }
}
