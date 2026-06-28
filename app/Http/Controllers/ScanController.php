<?php

namespace App\Http\Controllers;

use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    /**
     * Public scanner page — no auth required.
     */
    public function scanner(): View
    {
        return view('scanner');
    }

    /**
     * Handle QR scan: verify UUID, store session, redirect to profile.
     */
    public function scan(string $uuid): RedirectResponse
    {
        $profile = UserProfile::where('qr_token', $uuid)->firstOrFail();

        // Mark this browser session as having scanned this specific QR
        session()->put("qr_scanned_{$uuid}", true);

        return redirect()->route('profile.show', $uuid);
    }

    /**
     * Show the user profile — only accessible if scanned via QR in this session.
     */
    public function profile(string $uuid, Request $request): View|RedirectResponse
    {
        if (! $request->session()->get("qr_scanned_{$uuid}")) {
            abort(403, 'Access denied. Please scan the QR code to view this profile.');
        }

        $profile = UserProfile::with('images')->where('qr_token', $uuid)->firstOrFail();

        return view('profile', compact('profile'));
    }
}
