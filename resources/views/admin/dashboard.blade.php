<x-app-layout>
    <x-slot name="title">Analytics Dashboard</x-slot>

    {{-- ── Row 1: Stat cards ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Members --}}
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M12 12a4 4 0 100-8 4 4 0 000 8z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Members</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalMembers) }}</p>
            </div>
        </div>

        {{-- New This Month --}}
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">New This Month</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($newMembersThisMonth) }}</p>
            </div>
        </div>

        {{-- Total QR Scans --}}
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M3 17v2a2 2 0 002 2h2M17 21h2a2 2 0 002-2v-2" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total QR Scans</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($totalScans) }}</p>
            </div>
        </div>

        {{-- Today's Scans --}}
        <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Today's Scans</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($todayScans) }}</p>
            </div>
        </div>
    </div>

    {{-- ── Row 2: Most Scanned + Most Used Device ────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        {{-- Top 10 Most Scanned Members --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-4">Top 10 Most Scanned Members</p>
            @if ($mostScanned->isNotEmpty())
                <ul class="divide-y divide-gray-100">
                    @foreach ($mostScanned as $i => $member)
                        <li class="flex items-center gap-3 py-3">
                            <span class="w-6 text-center text-sm font-bold text-gray-400">#{{ $i + 1 }}</span>
                            <span class="flex-1 text-sm font-medium text-gray-800 truncate">{{ $member->name }}</span>
                            <span class="text-sm text-gray-500">{{ number_format($member->scans_count) }} scans</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-400 text-sm">No scan data yet.</p>
            @endif
        </div>

        {{-- Most Used Device --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-4">Devices</p>
            @if ($mostUsedDevice->isNotEmpty())
                <ul class="divide-y divide-gray-100">
                    @foreach ($mostUsedDevice as $device)
                        <li class="flex items-center gap-3 py-3">
                            <span class="flex-1 text-sm font-medium text-gray-800">{{ $device->device }}</span>
                            <span class="text-sm text-gray-500">{{ number_format($device->total) }} scans</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-400 text-sm">No scan data yet.</p>
            @endif
        </div>
    </div>

    {{-- ── Row 3: Scan Trend Chart ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-700">QR Scan Trend</h3>
            <span class="text-xs text-gray-400">Last 30 days</span>
        </div>
        <canvas id="scanChart" height="80"></canvas>
    </div>

    {{-- ── Row 4: Recent Scan Activity ───────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h3 class="text-base font-semibold text-gray-700 mb-4">Recent QR Scan Activity</h3>

        @if ($recentScans->isEmpty())
            <p class="text-gray-400 text-sm text-center py-8">No scan activity yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Member
                            </th>
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">IP
                                Address</th>
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Country
                            </th>
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">City</th>
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Browser
                            </th>
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">OS</th>
                            <th class="pb-3 pr-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Device
                            </th>
                            <th class="pb-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Date / Time
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($recentScans as $scan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Member image + name --}}
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-2">
                                        @php $img = $scan->user?->profileImage; @endphp
                                        @if ($img)
                                            <img src="{{ Storage::url($img->path) }}" alt=""
                                                class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($scan->user?->name ?? '?', 0, 1)) }}
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-700 whitespace-nowrap">
                                            {{ $scan->user?->name ?? '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 pr-4 font-mono text-gray-600 text-xs">{{ $scan->ip_address ?? '—' }}
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $scan->country }}</td>
                                <td class="py-3 pr-4 text-gray-600">{{ $scan->city }}</td>
                                <td class="py-3 pr-4">
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-xs font-medium">
                                        {{ $scan->browser }}
                                    </span>
                                </td>
                                <td class="py-3 pr-4 text-gray-600">{{ $scan->operating_system }}</td>
                                <td class="py-3 pr-4">
                                    <span
                                        class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                        {{ $scan->device === 'Mobile' ? 'bg-green-50 text-green-700' : ($scan->device === 'Tablet' ? 'bg-amber-50 text-amber-700' : 'bg-gray-100 text-gray-600') }}">
                                        {{ $scan->device }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-500 whitespace-nowrap text-xs">
                                    {{ $scan->scanned_at?->format('M d, Y') }}<br>
                                    <span class="text-gray-400">{{ $scan->scanned_at?->format('H:i:s') }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            const ctx = document.getElementById('scanChart').getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartData['labels']),
                    datasets: [{
                        label: 'QR Scans',
                        data: @json($chartData['values']),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.08)',
                        borderWidth: 2,
                        pointBackgroundColor: '#6366f1',
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        fill: true,
                        tension: 0.4,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.parsed.y} scan${ctx.parsed.y !== 1 ? 's' : ''}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#9ca3af',
                                maxRotation: 45
                            },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                precision: 0,
                            },
                            grid: {
                                color: '#f3f4f6'
                            },
                        },
                    },
                },
            });
        })();
    </script>

</x-app-layout>
