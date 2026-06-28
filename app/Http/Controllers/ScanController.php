<?php

namespace App\Http\Controllers;

use App\Models\QrScan;
use App\Models\UserProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;

class ScanController extends Controller
{
    public function scanner(): View
    {
        return view('scanner');
    }

    public function scan(string $uuid, Request $request): RedirectResponse
    {
        $profile = UserProfile::where('qr_token', $uuid)->firstOrFail();

        session()->put("qr_scanned_{$uuid}", true);

        $this->logScan($profile, $request);

        return redirect()->route('profile.show', $uuid);
    }

    public function profile(string $uuid, Request $request): View|RedirectResponse
    {
        if (! $request->session()->get("qr_scanned_{$uuid}")) {
            abort(403, 'Access denied. Please scan the QR code to view this profile.');
        }

        $profile = UserProfile::with('images')->where('qr_token', $uuid)->firstOrFail();

        return view('profile', compact('profile'));
    }

    private function logScan(UserProfile $profile, Request $request): void
    {
        try {
            // Dedup: ignore if the same profile was scanned in the last 15 seconds
            $isDuplicate = QrScan::where('user_profile_id', $profile->id)
                ->where('scanned_at', '>=', now()->subSeconds(15))
                ->exists();

            if ($isDuplicate) {
                return;
            }

            $agent = new Agent();
            $agent->setUserAgent($request->userAgent() ?? '');
            $agent->setHttpHeaders($request->headers->all());

            $device = match (true) {
                $agent->isPhone()  => 'Mobile',
                $agent->isTablet() => 'Tablet',
                default            => 'Desktop',
            };

            $ip = $request->ip();

            logger()->info('QR_DEBUG', [
                'ip()'            => $ip,
                'REMOTE_ADDR'     => $request->server('REMOTE_ADDR'),
                'x-forwarded-for' => $request->header('x-forwarded-for'),
                'x-real-ip'       => $request->header('x-real-ip'),
            ]);

            // Insert immediately so the redirect is fast, then update geo in the background
            $scan = QrScan::create([
                'user_profile_id'  => $profile->id,
                'ip_address'       => $ip,
                'country'          => 'Unknown',
                'city'             => 'Unknown',
                'browser'          => $agent->browser() ?: 'Unknown',
                'operating_system' => str_replace('OS', '', $agent->platform() ?: 'Unknown'),
                'device'           => $device,
                'scanned_at'       => now(),
            ]);

            // Geo lookup after insert — won't block the redirect
            $geo = $this->geoLookup($ip);
            if ($geo['country'] !== 'Unknown') {
                $scan->update(['country' => $geo['country'], 'city' => $geo['city']]);
            }

        } catch (\Throwable $e) {
            logger()->error('QR scan log failed: '.$e->getMessage());
        }
    }

    private function geoLookup(string $ip): array
    {
        $unknown = ['country' => 'Unknown', 'city' => 'Unknown'];

        // Private / loopback addresses cannot be geolocated
        if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $unknown;
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");

            if ($response->successful() && $response->json('status') === 'success') {
                return [
                    'country' => $response->json('country') ?: 'Unknown',
                    'city'    => $response->json('city') ?: 'Unknown',
                ];
            }
        } catch (\Throwable) {}

        return $unknown;
    }
}
