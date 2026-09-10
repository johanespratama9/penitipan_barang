<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS Kasir – Sarinah Street' }}</title>

    <!-- PWA metas -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0C1829">

    @livewireStyles

    <style>
        /* ─── Global Reset ─── */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            width: 100%;
            overflow: hidden;
            background: #0C1829;
            color: #EAF0FB;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            -webkit-tap-highlight-color: transparent;
            font-size: 14px;
        }

        #pos-root {
            height: 100%;
            width: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ─── Tailwind-like utilities for POS (no CDN needed) ─── */
        .hidden { display: none !important; }

        @media (min-width: 1024px) {
            .lg\:flex  { display: flex !important; }
            .lg\:hidden { display: none !important; }
        }

        .sm\:inline { display: none; }
        @media (min-width: 640px) {
            .sm\:inline { display: inline !important; }
        }

        /* ─── Toast notifications ─── */
        #pos-toasts {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
            max-width: 320px;
            width: calc(100% - 32px);
        }

        .pos-toast {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            opacity: 0;
            transform: translateX(32px);
            transition: opacity .25s ease, transform .25s ease;
            pointer-events: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,.4);
            word-break: break-word;
        }

        .pos-toast.show  { opacity: 1; transform: translateX(0); }
        .pos-toast.success { background: #059669; }
        .pos-toast.danger  { background: #DC2626; }
        .pos-toast.warning { background: #D97706; }
        .pos-toast.info    { background: #2563EB; }

        /* ─── Scrollbar ─── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #243358; border-radius: 99px; }

        /* ─── [x-cloak] ─── */
        [x-cloak] { display: none !important; }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div id="pos-toasts" aria-live="polite" aria-atomic="false"></div>

    <div id="pos-root">
        {{ $slot }}
    </div>

    @livewireScripts

    <script>
        // ── Toast listener for Livewire dispatch events ──
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('pos-notify', (data) => {
                // Livewire 3 passes event data as first argument (object or array)
                const payload  = Array.isArray(data) ? data[0] : data;
                const type     = payload.type    ?? 'info';
                const message  = payload.message ?? '';

                const container = document.getElementById('pos-toasts');
                if (!container) return;

                const el     = document.createElement('div');
                el.className = `pos-toast ${type}`;
                el.textContent = message;
                container.appendChild(el);

                // Trigger CSS transition
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => el.classList.add('show'));
                });

                setTimeout(() => {
                    el.classList.remove('show');
                    setTimeout(() => el.remove(), 300);
                }, 3200);
            });
        });
    </script>
</body>
</html>
