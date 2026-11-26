@php
    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <!-- Import Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS Style Block (Pure Vanilla CSS for guaranteed pixel-perfect layout) -->
    <style>
        .auth-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }

        /* Left Sidebar: Grocery Background & Brand Messaging */
        .auth-sidebar {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 55%;
            padding: 5rem 4rem;
            color: #FFFFFF;
            background-image: url('{{ asset("assets/grocery-login.jpg") }}');
            background-size: cover;
            background-position: center;
            box-sizing: border-box;
        }

        /* Dark Overlay to blur background and increase text readability */
        .auth-sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(6, 20, 39, 0.94) 0%, rgba(6, 20, 39, 0.82) 60%, rgba(228, 101, 46, 0.3) 100%);
            backdrop-filter: blur(2px);
            z-index: 1;
        }

        .auth-sidebar-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            width: 100%;
        }

        .auth-logo-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .auth-logo-wrapper {
            background: rgba(255, 255, 255, 0.12);
            padding: 1rem;
            border-radius: 1.75rem;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
            box-sizing: border-box;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }

        .auth-logo-img {
            height: 84px !important;
            width: 84px !important;
            object-fit: contain;
        }

        .auth-logo-text {
            display: flex;
            flex-direction: column;
        }

        .auth-brand-name {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 850;
            letter-spacing: -0.02em;
            color: #FFFFFF;
            line-height: 1;
        }

        .auth-brand-sub {
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: #E4652E;
            text-transform: uppercase;
            margin-top: 0.45rem;
        }

        .auth-headline-section {
            margin-top: auto;
            margin-bottom: auto;
            max-width: 520px;
        }

        .auth-headline {
            font-family: 'Outfit', sans-serif;
            font-size: 3.25rem;
            font-weight: 800;
            line-height: 1.2;
            color: #FFFFFF;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .auth-headline span {
            color: #E4652E;
        }

        .auth-subheadline {
            margin-top: 1.25rem;
            font-size: 1.1rem;
            line-height: 1.6;
            color: #E2E8F0;
            font-weight: 500;
        }

        .auth-footer {
            font-size: 0.85rem;
            color: #94A3B8;
        }

        .auth-footer p {
            margin: 4px 0;
        }

        /* Right Panel: Login Form Container */
        .auth-form-container {
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 5rem 4rem;
            background-color: #FFFFFF !important;
            box-sizing: border-box;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.03);
        }

        .auth-form-wrapper {
            width: 100%;
            max-width: 380px;
        }

        .auth-form-header {
            margin-bottom: 2.5rem;
        }

        .auth-form-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.5rem;
            font-weight: 800;
            color: #061427 !important;
            margin: 0;
            letter-spacing: -0.015em;
        }

        .auth-form-desc {
            margin-top: 0.5rem;
            font-size: 0.95rem;
            color: #64748B !important;
            font-weight: 500;
            line-height: 1.4;
        }

        /* Mobile Responsive Header Styles */
        .auth-mobile-header {
            display: none;
            text-align: center;
            margin-bottom: 2.5rem;
            width: 100%;
        }

        .auth-mobile-logo-wrapper {
            display: inline-flex;
            background: #FFFFFF;
            padding: 0.75rem;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #E2E8F0;
            margin-bottom: 1rem;
        }

        .auth-mobile-logo-img {
            height: 80px !important;
            width: 80px !important;
            object-fit: contain;
        }

        .auth-mobile-title {
            font-family: 'Outfit', sans-serif;
            font-size: 2.25rem;
            font-weight: 850;
            color: #061427;
            margin: 0;
            line-height: 1;
        }

        .auth-mobile-sub {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: #E4652E;
            text-transform: uppercase;
            margin-top: 0.4rem;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .auth-sidebar {
                width: 50%;
                padding: 3rem;
            }
            .auth-form-container {
                width: 50%;
                padding: 3rem;
            }
            .auth-headline {
                font-size: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            .auth-sidebar {
                display: none;
            }
            .auth-form-container {
                width: 100% !important;
                padding: 4rem 1.5rem !important;
                min-height: 100vh;
                background-color: #FFFFFF !important;
            }
            .auth-mobile-header {
                display: block;
            }
            .auth-form-header {
                display: none;
            }
        }

        /* RESET FILAMENT SIMPLE PAGE BOXES */
        .fi-simple-page {
            background-color: transparent !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
        
        .fi-simple-page-content {
            padding: 0 !important;
            width: 100% !important;
        }

        .fi-simple-page-content > .fi-header {
            display: none !important; /* Hide default header */
        }

        /* FORM FIELD AND BUTTON CUSTOMIZATION (Strict light theme enforcement) */
        .fi-auth-form .fi-input-wrp {
            border-radius: 9999px !important;
            padding: 4px 18px !important;
            border: 1.5px solid #CBD5E1 !important;
            background-color: #F8FAFC !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.2s ease-in-out !important;
        }
        
        /* Override dark mode style of inputs in case dark class is on HTML root */
        .fi-auth-form .fi-input-wrp,
        .dark .fi-auth-form .fi-input-wrp {
            border-color: #CBD5E1 !important;
            background-color: #F8FAFC !important;
        }

        .fi-auth-form .fi-input-wrp:focus-within,
        .dark .fi-auth-form .fi-input-wrp:focus-within {
            border-color: #E4652E !important;
            background-color: #FFFFFF !important;
            box-shadow: 0 0 0 3px rgba(228, 101, 46, 0.15) !important;
        }

        /* Input values and typed text color (Strictly dark text color) */
        .fi-auth-form input[type="text"],
        .fi-auth-form input[type="email"],
        .fi-auth-form input[type="password"],
        .dark .fi-auth-form input[type="text"],
        .dark .fi-auth-form input[type="email"],
        .dark .fi-auth-form input[type="password"] {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 500 !important;
            color: #0F172A !important;
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Checkbox styling (Restores and customizes the checkbox box) */
        .fi-auth-form input[type="checkbox"],
        .dark .fi-auth-form input[type="checkbox"] {
            border-radius: 0.375rem !important;
            border: 1.5px solid #CBD5E1 !important;
            background-color: #FFFFFF !important;
            color: #E4652E !important; /* checked state fill color */
            width: 1.25rem !important;
            height: 1.25rem !important;
            cursor: pointer !important;
            box-shadow: none !important;
            transition: all 0.2s ease-in-out !important;
            margin-right: 0.5rem !important;
        }

        .fi-auth-form input[type="checkbox"]:checked,
        .dark .fi-auth-form input[type="checkbox"]:checked {
            background-color: #E4652E !important;
            border-color: #E4652E !important;
        }

        .fi-auth-form input[type="checkbox"]:focus,
        .dark .fi-auth-form input[type="checkbox"]:focus {
            --tw-ring-color: #E4652E !important;
            border-color: #E4652E !important;
            box-shadow: 0 0 0 3px rgba(228, 101, 46, 0.15) !important;
        }

        /* Input Placeholders Styling (Guarantees placeholders show up in slate gray color) */
        .fi-auth-form input::placeholder,
        .dark .fi-auth-form input::placeholder {
            color: #64748B !important; /* Slightly darker than 94a3b8 for better visibility */
            opacity: 1 !important;
        }

        /* Form Labels Styling (Strictly visible dark slate text color - fixed class names) */
        .fi-auth-form label, 
        .fi-auth-form .fi-fo-field-label,
        .fi-auth-form .fi-fo-field-label-content,
        .fi-auth-form .fi-fo-field-label-content span,
        .dark .fi-auth-form label,
        .dark .fi-auth-form .fi-fo-field-label,
        .dark .fi-auth-form .fi-fo-field-label-content,
        .dark .fi-auth-form .fi-fo-field-label-content span {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            color: #334155 !important;
            margin-bottom: 0.4rem !important;
            display: inline-block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Red asterisks style */
        .fi-auth-form .fi-fo-field-label-required-mark,
        .fi-auth-form .text-danger-600,
        .fi-auth-form .text-danger-500,
        .dark .fi-auth-form .fi-fo-field-label-required-mark,
        .dark .fi-auth-form .text-danger-600,
        .dark .fi-auth-form .text-danger-500 {
            color: #EF4444 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .fi-auth-form a, 
        .fi-auth-form .fi-link,
        .dark .fi-auth-form a, 
        .dark .fi-auth-form .fi-link {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 600 !important;
            color: #E4652E !important;
            text-decoration: none !important;
        }

        .fi-auth-form a:hover,
        .fi-auth-form .fi-link:hover,
        .dark .fi-auth-form a:hover,
        .dark .fi-auth-form .fi-link:hover {
            color: #C9541F !important;
            text-decoration: underline !important;
        }

        /* Pill Rounded Orange Sign In Button */
        .fi-auth-form button[type="submit"],
        .fi-auth-form .fi-btn {
            font-family: 'Outfit', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.01em !important;
            border-radius: 9999px !important;
            padding: 12px 24px !important;
            background: linear-gradient(135deg, #E4652E 0%, #FF7A00 100%) !important;
            border: none !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(228, 101, 46, 0.3) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            cursor: pointer !important;
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        .fi-auth-form button[type="submit"]:hover,
        .fi-auth-form .fi-btn:hover {
            transform: translateY(-1.5px) !important;
            box-shadow: 0 6px 18px rgba(228, 101, 46, 0.45) !important;
            filter: brightness(1.05) !important;
        }

        .fi-auth-form button[type="submit"]:active,
        .fi-auth-form .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 8px rgba(228, 101, 46, 0.3) !important;
        }

        /* Clean remember-me checkbox alignment */
        .fi-auth-form .fi-checkbox-label,
        .dark .fi-auth-form .fi-checkbox-label,
        .fi-auth-form .fi-checkbox-label span,
        .dark .fi-auth-form .fi-checkbox-label span {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 500 !important;
            color: #475569 !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        /* Fix the password reveal button / view icon colors */
        .fi-auth-form .fi-input-wrp button,
        .fi-auth-form .fi-input-wrp svg,
        .dark .fi-auth-form .fi-input-wrp button,
        .dark .fi-auth-form .fi-input-wrp svg {
            color: #64748B !important;
        }
    </style>

    <div class="auth-container">
        
        <!-- Left Side Panel (Hidden on Mobile) -->
        <div class="auth-sidebar">
            <div class="auth-sidebar-content">
                
                <!-- Logo & Brand Header (Enlarged) -->
                <div class="auth-logo-section">
                    <div class="auth-logo-wrapper">
                        <img src="{{ asset('assets/logo.png') }}" alt="Jay Tulsi Agency" class="auth-logo-img">
                    </div>
                    <div class="auth-logo-text">
                        <span class="auth-brand-name">JAY TULSI</span>
                        <span class="auth-brand-sub">Vinayak Stores</span>
                    </div>
                </div>

                <!-- Middle Headline -->
                <div class="auth-headline-section">
                    <h1 class="auth-headline">Manage your <span>retail</span> & <span>B2B</span> supply store.</h1>
                    <p class="auth-subheadline">
                        Securely manage product catalogues, category listings, promotional campaigns, store details, and view customer stats in real-time.
                    </p>
                </div>

                <!-- Footer Disclaimer -->
                <div class="auth-footer">
                    <p style="font-weight: 600; color: #FFFFFF;">&copy; {{ date('Y') }} Jay Tulsi Agency & Vinayak Stores.</p>
                    <p>Authorized Personnel Only &bull; All Actions Are Logged</p>
                </div>

            </div>
        </div>

        <!-- Right Side Panel (Contains the login form) -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper">

                <!-- Mobile Only Header -->
                <div class="auth-mobile-header">
                    <div class="auth-mobile-logo-wrapper">
                        <img src="{{ asset('assets/logo.png') }}" alt="Jay Tulsi" class="auth-mobile-logo-img">
                    </div>
                    <h2 class="auth-mobile-title">JAY TULSI</h2>
                    <p class="auth-mobile-sub">Vinayak Stores</p>
                </div>

                <!-- Desktop Header -->
                <div class="auth-form-header">
                    <h2 class="auth-form-title">Sign In</h2>
                    <p class="auth-form-desc">
                        Welcome back! Please enter your admin account credentials.
                    </p>
                </div>

                <!-- Filament Login Page Slot -->
                <div class="fi-auth-form">
                    {{ $slot }}
                </div>

            </div>
        </div>

    </div>

    {{-- Login button loading spinner --}}
    <style>
        /* Spinner inside the sign-in button */
        .fi-auth-form button[type="submit"] .jt-login-spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2.5px solid rgba(255, 255, 255, 0.35);
            border-top-color: #FFFFFF;
            border-radius: 50%;
            animation: jt-spin 0.6s linear infinite;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .fi-auth-form button[type="submit"].is-loading {
            pointer-events: none;
            opacity: 0.88;
        }

        .fi-auth-form button[type="submit"].is-loading .jt-login-spinner {
            display: inline-block;
        }

        .fi-auth-form button[type="submit"].is-loading .jt-btn-label {
            display: none;
        }

        .fi-auth-form button[type="submit"].is-loading .jt-btn-loading-label {
            display: inline;
        }

        .fi-auth-form button[type="submit"] .jt-btn-loading-label {
            display: none;
        }

        @keyframes jt-spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inject spinner and loading label into the submit button
            const form = document.querySelector('.fi-auth-form form');
            if (!form) return;

            const btn = form.querySelector('button[type="submit"]');
            if (!btn) return;

            // Wrap existing label
            const originalContent = btn.innerHTML;
            btn.innerHTML = `
                <span class="jt-login-spinner"></span>
                <span class="jt-btn-label">${originalContent}</span>
                <span class="jt-btn-loading-label">Signing in…</span>
            `;

            form.addEventListener('submit', function () {
                btn.classList.add('is-loading');
            });

            // If Livewire validation fails, remove loading state
            document.addEventListener('livewire:init', function () {
                Livewire.hook('request', ({ fail }) => {
                    fail(() => {
                        btn.classList.remove('is-loading');
                    });
                });
            });
        });
    </script>
</x-filament-panels::layout.base>
