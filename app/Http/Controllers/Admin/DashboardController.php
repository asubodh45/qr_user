<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrScan;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalMembers = UserProfile::count();

        $newMembersThisMonth = UserProfile::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        $totalScans = QrScan::count();

        $todayScans = QrScan::whereDate('scanned_at', today())->count();

        $mostScanned = UserProfile::withCount('scans')
            ->orderByDesc('scans_count')
            ->first();

        $mostUsedDevice = QrScan::select('device', DB::raw('count(*) as total'))
            ->groupBy('device')
            ->orderByDesc('total')
            ->first();

        $recentScans = QrScan::with(['user', 'user.profileImage'])
            ->orderByDesc('scanned_at')
            ->limit(20)
            ->get();

        $chartData = $this->chartData();

        return view('admin.dashboard', compact(
            'totalMembers',
            'newMembersThisMonth',
            'totalScans',
            'todayScans',
            'mostScanned',
            'mostUsedDevice',
            'recentScans',
            'chartData',
        ));
    }

    private function chartData(): array
    {
        $days = collect(range(29, 0))->map(fn ($d) => now()->subDays($d)->toDateString());

        $scans = QrScan::selectRaw('DATE(scanned_at) as date, COUNT(*) as total')
            ->where('scanned_at', '>=', now()->subDays(29)->startOfDay())
            ->groupByRaw('DATE(scanned_at)')
            ->pluck('total', 'date');

        return [
            'labels' => $days->map(fn ($d) => \Carbon\Carbon::parse($d)->format('M d'))->values()->all(),
            'values' => $days->map(fn ($d) => (int) ($scans[$d] ?? 0))->values()->all(),
        ];
    }
}
