<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TwoFactorAuthenticationController extends Controller
{
    public function show(Request $request): View
    {
        return view('auth.two-factor.show', [
            'twoFactorEnabled' => (bool) $request->user()->two_factor_confirmed_at,
        ]);
    }

    public function store(Request $request): Response
    {
        if ($request->user()->two_factor_secret) {
            return response('2FA already setup', 400);
        }

        $secret = TwoFactorService::generateSecret();
        $backupCodes = TwoFactorService::generateBackupCodes();

        $request->user()->update([
            'two_factor_secret' => $secret,
            'two_factor_backup_codes' => TwoFactorService::encodeBackupCodes($backupCodes),
        ]);

        return response()->json([
            'secret' => $secret,
            'backup_codes' => $backupCodes,
        ]);
    }

    public function confirm(Request $request): Response
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        if (! TwoFactorService::verifyCode($user->two_factor_secret, $request->code)) {
            // In production, use proper TOTP library to verify
            return response()->json(['error' => 'Invalid code'], 401);
        }

        $user->update(['two_factor_confirmed_at' => now()]);

        return response()->json(['message' => '2FA confirmed successfully']);
    }

    public function disable(Request $request): Response
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $request->user()->update([
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return response()->json(['message' => '2FA disabled']);
    }
}
