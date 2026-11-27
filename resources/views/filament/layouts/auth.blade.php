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
            background-image: url('{{ asset("assets/mega-sale-banner-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            box-sizing: border-box;
        }

        /* Dark Forest Green Overlay for crisp readability */
        .auth-sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 58, 29, 0.95) 0%, rgba(20, 83, 45, 0.88) 58%, rgba(63, 156, 30, 0.48) 100%);
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
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: #FFFFFF;
            line-height: 1;
        }

        .auth-brand-sub {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            color: #65D138;
            text-transform: uppercase;
            margin-top: 0.45rem;
        }

        .auth-headline-section {
            margin-top: auto;
            margin-bottom: auto;
            max-width: 520px;
        }

        .auth-headline {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 3.1rem;
            font-weight: 800;
            line-height: 1.16;
            color: #FFFFFF;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .auth-headline span {
            color: #65D138;
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
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            color: #14532D !important;
            margin: 0;
            letter-spacing: -0.025em;
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
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.1rem;
            font-weight: 800;
            color: #14532D;
            margin: 0;
            line-height: 1;
        }

        .auth-mobile-sub {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: #3F9C1E;
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
                position: relative;
                width: 100% !important;
                padding: 2rem 1rem !important;
                min-height: 100vh;
                background-color: #14532D !important;
                background-image: url('{{ asset("assets/mega-sale-banner-bg.jpg") }}') !important;
                background-size: cover !important;
                background-position: center !important;
            }
            .auth-form-container::before {
                content: '';
                position: absolute;
                inset: 0;
                background: linear-gradient(155deg, rgba(15, 58, 29, 0.9) 0%, rgba(20, 83, 45, 0.82) 55%, rgba(63, 156, 30, 0.68) 100%);
                z-index: 1;
            }
            .auth-form-wrapper {
                position: relative;
                z-index: 2;
                background: rgba(255, 255, 255, 0.86);
                -webkit-backdrop-filter: blur(18px);
                backdrop-filter: blur(18px);
                border: 1px solid rgba(255, 255, 255, 0.62);
                border-radius: 24px;
                padding: 2.75rem 1.375rem;
                box-shadow: 0 24px 52px rgba(10, 34, 18, 0.34);
                box-sizing: border-box;
            }
            .auth-mobile-header {
                display: block;
                margin-bottom: 1.5rem;
            }
            .auth-mobile-logo-wrapper {
                margin-bottom: 0.75rem;
            }
            .auth-form-header {
                display: none;
            }
        }

        /* Forgot Password & Create Account Links */
        .auth-extra-links {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(20, 83, 45, 0.12);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.65rem;
            text-align: center;
            font-size: 0.875rem;
        }

        .auth-link-btn {
            background: transparent;
            border: none;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.875rem;
            font-weight: 700;
            color: #14532D;
            cursor: pointer;
            text-decoration: none;
        }

        .auth-link-btn:hover {
            color: #3F9C1E;
            text-decoration: underline;
        }

        .auth-create-row {
            color: #475569;
            font-weight: 500;
        }

        .auth-create-row button {
            color: #3F9C1E;
            margin-left: 4px;
        }

        /* Auth Helper Modal (Forgot Password / Create Account) */
        .auth-helper-backdrop {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(15, 46, 26, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .auth-helper-backdrop.is-open {
            display: flex;
        }

        .auth-helper-card {
            width: 100%;
            max-width: 400px;
            background: #FFFFFF;
            border-radius: 20px;
            padding: 1.75rem 1.5rem;
            box-shadow: 0 24px 50px rgba(10, 34, 18, 0.28);
            border: 1px solid #DCECD3;
            position: relative;
        }

        .auth-helper-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 1px solid #E5ECE1;
            background: #F4FAF0;
            color: #14532D;
            font-size: 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .auth-helper-card h3 {
            margin: 0 0 6px;
            font-size: 1.25rem;
            font-weight: 800;
            color: #14532D;
        }

        .auth-helper-card p {
            margin: 0 0 16px;
            font-size: 0.85rem;
            color: #64748B;
            line-height: 1.45;
        }

        .auth-helper-field {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-bottom: 12px;
        }

        .auth-helper-field label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
        }

        .auth-helper-field input {
            height: 40px;
            border-radius: 10px;
            border: 1.5px solid #D5E4CE;
            padding: 0 12px;
            font-size: 0.875rem;
            font-family: inherit;
            color: #0F172A;
            outline: none;
        }

        .auth-helper-field input:focus {
            border-color: #3F9C1E;
        }

        .auth-helper-submit {
            width: 100%;
            height: 42px;
            border: none;
            border-radius: 9999px;
            background: linear-gradient(135deg, #14532D 0%, #3F9C1E 100%);
            color: #FFFFFF;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 4px;
        }

        .auth-helper-status {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            background: #EBF7E3;
            color: #14532D;
            font-size: 0.82rem;
            font-weight: 600;
            display: none;
        }

        .auth-helper-status.is-visible {
            display: block;
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
            border-color: #3F9C1E !important;
            background-color: #FFFFFF !important;
            box-shadow: 0 0 0 3px rgba(63, 156, 30, 0.16) !important;
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
            color: #3F9C1E !important; /* checked state fill color */
            width: 1.25rem !important;
            height: 1.25rem !important;
            cursor: pointer !important;
            box-shadow: none !important;
            transition: all 0.2s ease-in-out !important;
            margin-right: 0.5rem !important;
        }

        .fi-auth-form input[type="checkbox"]:checked,
        .dark .fi-auth-form input[type="checkbox"]:checked {
            background-color: #3F9C1E !important;
            border-color: #3F9C1E !important;
        }

        .fi-auth-form input[type="checkbox"]:focus,
        .dark .fi-auth-form input[type="checkbox"]:focus {
            --tw-ring-color: #3F9C1E !important;
            border-color: #3F9C1E !important;
            box-shadow: 0 0 0 3px rgba(63, 156, 30, 0.16) !important;
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
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 600 !important;
            color: #14532D !important;
            text-decoration: none !important;
        }

        .fi-auth-form a:hover,
        .fi-auth-form .fi-link:hover,
        .dark .fi-auth-form a:hover,
        .dark .fi-auth-form .fi-link:hover {
            color: #3F9C1E !important;
            text-decoration: underline !important;
        }

        /* Pill Rounded Green Sign In Button */
        .fi-auth-form button[type="submit"],
        .fi-auth-form .fi-btn {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-weight: 700 !important;
            font-size: 1rem !important;
            letter-spacing: 0.01em !important;
            border-radius: 9999px !important;
            padding: 12px 24px !important;
            background: linear-gradient(135deg, #14532D 0%, #3F9C1E 100%) !important;
            border: none !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(20, 83, 45, 0.28) !important;
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
            box-shadow: 0 6px 18px rgba(20, 83, 45, 0.38) !important;
            filter: brightness(1.05) !important;
        }

        .fi-auth-form button[type="submit"]:active,
        .fi-auth-form .fi-btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 8px rgba(20, 83, 45, 0.28) !important;
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
                
                <!-- Logo & Brand Header -->
                <div class="auth-logo-section">
                    <div class="auth-logo-wrapper" style="width: 72px; height: 72px; border-radius: 1.25rem;">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#65D138" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                        </svg>
                    </div>
                    <div class="auth-logo-text">
                        <span class="auth-brand-name">VIZORA</span>
                        <span class="auth-brand-sub">VIZORA FOODS</span>
                    </div>
                </div>

                <!-- Middle Headline -->
                <div class="auth-headline-section">
                    <h1 class="auth-headline">Manage your <span>fresh</span> &amp; <span>grocery</span> store.</h1>
                    <p class="auth-subheadline">
                        Securely manage product catalogues, category listings, promotional campaigns, store details, and view customer stats in real-time.
                    </p>
                </div>

                <!-- Footer Disclaimer -->
                <div class="auth-footer">
                    <p style="font-weight: 600; color: #FFFFFF;">&copy; {{ date('Y') }} VIZORA FOODS. All rights reserved.</p>
                    <p>Authorized Personnel Only &bull; All Actions Are Logged</p>
                </div>

            </div>
        </div>

        <!-- Right Side Panel (Contains the login form) -->
        <div class="auth-form-container">
            <div class="auth-form-wrapper">

                <!-- Mobile Only Header -->
                <div class="auth-mobile-header">
                    <div class="auth-mobile-logo-wrapper" style="background: #EBF7E3; border-color: #DCECD3;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#14532D" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                            <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                        </svg>
                    </div>
                    <h2 class="auth-mobile-title">VIZORA</h2>
                    <p class="auth-mobile-sub">VIZORA FOODS</p>
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

                <!-- Forgot Password & Create Account Links (Desktop + Mobile) -->
                <div class="auth-extra-links">
                    <button type="button" class="auth-link-btn" onclick="openAuthHelper('forgot')">
                        Forgot Password?
                    </button>
                    <div class="auth-create-row">
                        Don't have an account?
                        <button type="button" class="auth-link-btn" onclick="openAuthHelper('register')">
                            Create Account
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Forgot Password & Create Account Modal -->
    <div id="authHelperModal" class="auth-helper-backdrop" onclick="if(event.target===this) closeAuthHelper()">
        <div class="auth-helper-card">
            <button type="button" class="auth-helper-close" onclick="closeAuthHelper()" aria-label="Close">&times;</button>

            <div id="authForgotPane" style="display:none;">
                <h3>Reset Password</h3>
                <p>Enter your registered email address and our VIZORA FOODS team will send you password reset instructions.</p>
                <form onsubmit="submitAuthHelper(event, 'forgot')">
                    <div class="auth-helper-field">
                        <label>Email Address *</label>
                        <input type="email" required placeholder="you@vizorafoods.com">
                    </div>
                    <button type="submit" class="auth-helper-submit">Send Reset Link</button>
                    <div id="forgotStatus" class="auth-helper-status">
                        Reset instructions request received! Check your email or contact VIZORA FOODS admin support.
                    </div>
                </form>
            </div>

            <div id="authRegisterPane" style="display:none;">
                <h3>Create Account</h3>
                <p>Join VIZORA FOODS to manage your orders and storefront access.</p>
                <form onsubmit="submitAuthHelper(event, 'register')">
                    <div class="auth-helper-field">
                        <label>Full Name *</label>
                        <input type="text" required placeholder="e.g. Aarav Patel">
                    </div>
                    <div class="auth-helper-field">
                        <label>Email Address *</label>
                        <input type="email" required placeholder="you@example.com">
                    </div>
                    <div class="auth-helper-field">
                        <label>Mobile Number *</label>
                        <input type="tel" required placeholder="10-digit mobile number">
                    </div>
                    <div class="auth-helper-field">
                        <label>Password *</label>
                        <input type="password" required placeholder="Create a password">
                    </div>
                    <button type="submit" class="auth-helper-submit">Create Account</button>
                    <div id="registerStatus" class="auth-helper-status">
                        Account request submitted! Our VIZORA FOODS team will activate your access shortly.
                    </div>
                </form>
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

        function openAuthHelper(mode) {
            const modal = document.getElementById('authHelperModal');
            const forgot = document.getElementById('authForgotPane');
            const reg = document.getElementById('authRegisterPane');
            document.getElementById('forgotStatus').classList.remove('is-visible');
            document.getElementById('registerStatus').classList.remove('is-visible');
            forgot.style.display = mode === 'forgot' ? 'block' : 'none';
            reg.style.display = mode === 'register' ? 'block' : 'none';
            modal.classList.add('is-open');
        }

        function closeAuthHelper() {
            document.getElementById('authHelperModal').classList.remove('is-open');
        }

        function submitAuthHelper(e, mode) {
            e.preventDefault();
            const statusId = mode === 'forgot' ? 'forgotStatus' : 'registerStatus';
            document.getElementById(statusId).classList.add('is-visible');
        }
    </script>
</x-filament-panels::layout.base>
