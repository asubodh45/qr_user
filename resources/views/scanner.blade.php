<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Scanner — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- html5-qrcode library --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>

<body
    class="bg-gray-900 text-white font-sans antialiased min-h-screen flex flex-col items-center justify-center px-4 py-12">

    <div class="w-full max-w-sm">

        <div class="text-center mb-8">
            <svg class="w-12 h-12 text-indigo-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 7V5a2 2 0 012-2h2M17 3h2a2 2 0 012 2v2M3 17v2a2 2 0 002 2h2M17 21h2a2 2 0 002-2v-2" />
            </svg>
            <h1 class="text-2xl font-bold">QR Scanner</h1>
            <p class="text-sm text-gray-400 mt-1">Point the camera at a QR code to access the employee profile.</p>
        </div>

        {{-- Scanner viewport --}}
        <div class="rounded-2xl overflow-hidden border-2 border-indigo-500/50 shadow-xl shadow-indigo-500/10">
            <div id="qr-reader" class="w-full"></div>
        </div>

        {{-- Status messages --}}
        <div id="qr-status" class="mt-5 text-center text-sm text-gray-400">
            Waiting for camera access…
        </div>

        <div id="qr-result"
            class="mt-4 hidden bg-indigo-600/20 border border-indigo-500/30 rounded-xl px-4 py-3 text-center">
            <p class="text-indigo-300 text-sm font-medium">QR detected! Redirecting…</p>
        </div>


    </div>

    <script>
        (function() {
            let scanned = false;

            const statusEl = document.getElementById('qr-status');
            const resultEl = document.getElementById('qr-result');

            const html5QrCode = new Html5Qrcode('qr-reader');

            const config = {
                fps: 10,
                qrbox: {
                    width: 240,
                    height: 240
                },
                aspectRatio: 1.0,
            };

            function onScanSuccess(decodedText) {
                if (scanned) return;

                // Only handle URLs that match our /scan/{uuid} route pattern
                const scanPattern = /\/scan\/[0-9a-f-]{36}$/i;
                if (!scanPattern.test(decodedText) && !decodedText.startsWith(window.location.origin + '/scan/')) {
                    statusEl.textContent = 'Invalid QR code. Please scan a valid employee QR.';
                    return;
                }

                scanned = true;
                resultEl.classList.remove('hidden');
                statusEl.textContent = '';

                html5QrCode.stop().catch(() => {});

                // Redirect to the scanned URL (our /scan/{uuid} route)
                window.location.href = decodedText;
            }

            function onScanFailure() {
                // silent — fires on every failed frame
            }

            html5QrCode.start({
                    facingMode: 'environment'
                },
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                statusEl.textContent = 'Camera active — hold steady over a QR code.';
            }).catch(err => {
                statusEl.innerHTML =
                    '<span class="text-red-400">Camera access denied. Please allow camera permissions and reload.</span>';
                console.error(err);
            });
        })();
    </script>

</body>

</html>
