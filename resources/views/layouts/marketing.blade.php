<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ff9800">
    <title>@yield('title', config('app.name', 'Budget App'))</title>
    <style>
        :root {
            --ink: #2f2458;
            --ink-soft: rgba(255, 245, 240, 0.88);
            --line: #49286c;
            --line-soft: rgba(255, 255, 255, 0.22);
            --gold: #ffbf3d;
            --gold-deep: #f48d15;
            --panel: rgba(255, 255, 255, 0.1);
            --panel-strong: rgba(255, 255, 255, 0.14);
            --success: #0f766e;
            --danger: #c2410c;
        }

        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: white;
            background:
                linear-gradient(135deg, rgba(255, 205, 84, 0.16), transparent 30%),
                linear-gradient(315deg, rgba(122, 101, 255, 0.18), transparent 26%),
                linear-gradient(135deg, #ff9800 0%, #ff9421 36%, #ff8c6d 36%, #c46bb8 72%, #7865ff 100%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(1240px, calc(100% - 28px));
            margin: 0 auto;
            padding: 22px 0;
        }

        .hero {
            position: relative;
            overflow: hidden;
            min-height: calc(100vh - 44px);
            border: 2px solid rgba(255, 193, 66, 0.55);
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.07), transparent 30%),
                linear-gradient(225deg, rgba(83, 62, 154, 0.1), transparent 36%);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: auto auto -18% -10%;
            width: 46%;
            height: 68%;
            background: linear-gradient(45deg, rgba(255, 182, 90, 0.22), rgba(255, 255, 255, 0));
            transform: rotate(45deg);
            pointer-events: none;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: -18% -12% auto auto;
            width: 42%;
            height: 70%;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.12), rgba(255, 255, 255, 0));
            transform: rotate(45deg);
            pointer-events: none;
        }

        .nav {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 26px 54px 10px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .brand-with-logo span {
            display: block;
            max-width: 260px;
            line-height: 1.05;
        }

        .marketing-brand-logo {
            width: 138px;
            height: 138px;
            object-fit: contain;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.96);
            padding: 8px;
            border: 1px solid rgba(255, 255, 255, 0.32);
            box-shadow: 0 18px 34px rgba(55, 28, 90, 0.2);
        }

        .menu {
            display: flex;
            gap: 48px;
            flex-wrap: wrap;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .menu a {
            opacity: 0.96;
        }

        .menu a.active {
            text-decoration: underline;
            text-underline-offset: 6px;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .marketing-notices {
            padding: 8px 54px 0;
            display: grid;
            gap: 12px;
        }

        .marketing-flash {
            padding: 14px 18px;
            border-radius: 18px;
            border: 2px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            font-weight: 600;
        }

        .marketing-flash.success {
            border-color: rgba(15, 118, 110, 0.28);
            background: rgba(15, 118, 110, 0.18);
        }

        .marketing-flash.error {
            border-color: rgba(194, 65, 12, 0.34);
            background: rgba(194, 65, 12, 0.16);
        }

        .showcase-layout {
            display: grid;
            grid-template-columns: 0.9fr 1.15fr;
            align-items: center;
            gap: 34px;
            padding: 34px 54px 64px;
        }

        .showcase-copy {
            max-width: 470px;
        }

        .showcase-badge {
            display: inline-flex;
            align-items: center;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 2px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 18px;
            font-weight: 700;
        }

        .showcase-copy h1 {
            margin: 0 0 16px;
            font-size: clamp(2.7rem, 5.8vw, 5rem);
            line-height: 0.94;
            letter-spacing: -0.05em;
        }

        .showcase-copy p,
        .detail-card p,
        .detail-card li,
        .field span,
        .inline-links a,
        .auth-note {
            color: var(--ink-soft);
            font-size: 1.05rem;
            line-height: 1.7;
        }

        .showcase-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .primary-link,
        .alt-link,
        .primary-button,
        .secondary-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 58px;
            padding: 0 22px;
            border-radius: 999px;
            font-size: 1.05rem;
            font-weight: 800;
            cursor: pointer;
        }

        .primary-link,
        .primary-button {
            border: 4px solid var(--line);
            background: var(--gold);
            color: var(--ink);
            box-shadow: 0 12px 20px rgba(72, 40, 108, 0.12);
        }

        .alt-link,
        .secondary-button {
            border: 2px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .showcase-form {
            display: grid;
            gap: 12px;
            width: min(100%, 340px);
            margin-top: 20px;
        }

        .stack-form {
            display: grid;
            gap: 14px;
        }

        .form-grid-two {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .field {
            display: grid;
            gap: 7px;
        }

        .field span {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            min-height: 54px;
            padding: 0 16px;
            border: none;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.95);
            color: var(--ink);
            font-size: 1rem;
            box-shadow: 0 12px 28px rgba(112, 39, 17, 0.08);
        }

        .field textarea {
            min-height: 112px;
            padding: 14px 16px;
            resize: vertical;
        }

        .field input::placeholder,
        .field textarea::placeholder {
            color: rgba(47, 36, 88, 0.42);
        }

        .inline-check {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: white;
        }

        .inline-check input {
            width: 20px;
            height: 20px;
            accent-color: var(--gold-deep);
        }

        .inline-links {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .inline-links a {
            text-decoration: underline;
            text-underline-offset: 5px;
        }

        .showcase-visual {
            position: relative;
            min-height: 620px;
        }

        .illustration-stage {
            position: relative;
            width: 100%;
            min-height: 620px;
        }

        .float-dot,
        .ring,
        .plus-shape {
            position: absolute;
            border-radius: 999px;
            border: 4px solid var(--line);
            background: rgba(255, 255, 255, 0.88);
        }

        .float-dot.tiny {
            width: 16px;
            height: 16px;
            border-width: 3px;
        }

        .float-dot.small {
            width: 24px;
            height: 24px;
        }

        .ring {
            width: 26px;
            height: 26px;
            background: transparent;
        }

        .plus-shape {
            width: 40px;
            height: 14px;
        }

        .plus-shape::before {
            content: "";
            position: absolute;
            inset: -13px auto auto 11px;
            width: 14px;
            height: 40px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.88);
            border: 4px solid var(--line);
        }

        .device-laptop {
            position: absolute;
            top: 124px;
            left: 52px;
            width: 508px;
            height: 292px;
            border-radius: 22px;
            border: 4px solid var(--line);
            background: linear-gradient(180deg, #ffcf55, #f9b937);
            box-shadow: 0 24px 50px rgba(72, 40, 108, 0.14);
        }

        .device-laptop::before {
            content: "";
            position: absolute;
            inset: 20px 20px 42px;
            border-radius: 18px;
            border: 4px solid var(--line);
            background: linear-gradient(135deg, #efefff 0%, #d2d4f5 100%);
        }

        .device-laptop::after {
            content: "";
            position: absolute;
            left: 44px;
            right: 44px;
            bottom: -18px;
            height: 34px;
            border-radius: 0 0 26px 26px;
            border: 4px solid var(--line);
            background: linear-gradient(180deg, #ffb842, #f48d15);
        }

        .camera-bar {
            position: absolute;
            top: 10px;
            left: 172px;
            width: 140px;
            height: 18px;
            border-radius: 999px;
            border: 4px solid var(--line);
            background: rgba(255, 255, 255, 0.95);
        }

        .camera-bar::before {
            content: "";
            position: absolute;
            left: -28px;
            top: -4px;
            width: 18px;
            height: 18px;
            border-radius: 999px;
            border: 4px solid var(--line);
            background: #fff3d1;
        }

        .device-phone {
            position: absolute;
            top: 170px;
            right: 34px;
            width: 164px;
            height: 240px;
            border-radius: 26px;
            border: 4px solid var(--line);
            background: linear-gradient(180deg, #ffcf55, #f8bb3f);
            box-shadow: 0 24px 44px rgba(72, 40, 108, 0.16);
        }

        .device-phone::before {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: 16px;
            border: 4px solid var(--line);
            background: linear-gradient(135deg, #efefff 0%, #d2d4f5 100%);
        }

        .device-phone::after {
            content: "";
            position: absolute;
            left: 66px;
            bottom: 10px;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            border: 4px solid var(--line);
            background: rgba(255, 255, 255, 0.9);
        }

        .card-graphic {
            position: absolute;
            top: 178px;
            left: 134px;
            width: 160px;
            height: 92px;
            border-radius: 18px;
            border: 4px solid var(--line);
            background: linear-gradient(135deg, #df8bf4 0%, #b959d4 100%);
            transform: rotate(-14deg);
            box-shadow: 0 18px 32px rgba(72, 40, 108, 0.18);
        }

        .card-graphic::before {
            content: "";
            position: absolute;
            top: 20px;
            left: 20px;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 4px solid var(--line);
            background: rgba(255, 255, 255, 0.95);
        }

        .card-graphic::after {
            content: "";
            position: absolute;
            right: 16px;
            bottom: 22px;
            width: 72px;
            height: 16px;
            border-radius: 999px;
            background: rgba(86, 39, 134, 0.35);
        }

        .coin {
            position: absolute;
            top: 178px;
            left: 334px;
            width: 116px;
            height: 116px;
            border-radius: 999px;
            border: 4px solid var(--line);
            background: radial-gradient(circle at 35% 32%, #ffe9a8, #ffc541 56%, #f09c12 100%);
            box-shadow: 0 18px 32px rgba(72, 40, 108, 0.14);
        }

        .coin::before {
            content: "$";
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            color: #883c00;
            font-size: 3.5rem;
            font-weight: 900;
        }

        .cash {
            position: absolute;
            top: 218px;
            right: 56px;
            width: 90px;
            height: 54px;
            border-radius: 10px;
            border: 4px solid var(--line);
            background: linear-gradient(135deg, #ffd16a, #ffb948);
            transform: rotate(18deg);
        }

        .cash::before,
        .cash::after {
            content: "";
            position: absolute;
            inset: 8px 12px;
            border-radius: 8px;
            border: 4px solid rgba(86, 39, 134, 0.72);
        }

        .cash::after {
            inset: 12px 22px;
            border-radius: 999px;
        }

        .wallet {
            position: absolute;
            left: 102px;
            bottom: 88px;
            width: 66px;
            height: 112px;
            border-radius: 12px;
            border: 4px solid var(--line);
            background: linear-gradient(180deg, #e37cec, #b239ca);
        }

        .wallet::before {
            content: "";
            position: absolute;
            top: 14px;
            left: 12px;
            width: 8px;
            height: 42px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.9);
        }

        .wallet::after {
            content: "";
            position: absolute;
            top: 22px;
            right: 12px;
            width: 18px;
            height: 30px;
            border-radius: 6px;
            border: 4px solid var(--line);
            background: rgba(255, 255, 255, 0.88);
        }

        .detail-grid {
            position: absolute;
            right: 10px;
            bottom: 0;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            width: min(100%, 420px);
        }

        .detail-card {
            padding: 18px;
            border-radius: 22px;
            border: 2px solid var(--line-soft);
            background: var(--panel);
            backdrop-filter: blur(10px);
            box-shadow: 0 16px 32px rgba(72, 40, 108, 0.12);
        }

        .detail-card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1.1rem;
        }

        .detail-card ul {
            margin: 0;
            padding-left: 18px;
        }

        .media-frame {
            display: grid;
            place-items: center;
            padding: 34px;
            border-radius: 28px;
            border: 2px solid rgba(255, 255, 255, 0.24);
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            min-height: 420px;
            box-shadow: 0 20px 42px rgba(72, 40, 108, 0.14);
        }

        .media-frame img {
            width: min(100%, 440px);
            height: auto;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.96);
            padding: 14px;
            box-shadow: 0 22px 44px rgba(72, 40, 108, 0.18);
        }

        @media (max-width: 1100px) {
            .showcase-layout {
                grid-template-columns: 1fr;
            }

            .showcase-copy {
                max-width: none;
            }

            .showcase-visual,
            .illustration-stage {
                min-height: 720px;
            }

            .detail-grid {
                position: relative;
                right: auto;
                bottom: auto;
                margin-top: 460px;
                width: 100%;
            }
        }

        @media (max-width: 760px) {
            .nav {
                padding: 22px 22px 0;
                flex-direction: column;
                align-items: flex-start;
            }

            .menu {
                gap: 18px;
            }

            .marketing-notices,
            .showcase-layout {
                padding-left: 22px;
                padding-right: 22px;
            }

            .showcase-layout {
                padding-top: 24px;
                padding-bottom: 40px;
            }

            .form-grid-two,
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .showcase-form,
            .primary-link,
            .alt-link,
            .primary-button,
            .secondary-button {
                width: 100%;
            }

            .brand-with-logo span {
                max-width: none;
            }

            .showcase-visual,
            .illustration-stage {
                min-height: 640px;
            }

            .device-laptop {
                left: 8px;
                width: calc(100% - 92px);
                height: 250px;
            }

            .camera-bar {
                left: calc(50% - 70px);
            }

            .device-phone {
                top: 190px;
                right: 4px;
                width: 132px;
                height: 206px;
            }

            .card-graphic {
                top: 194px;
                left: 72px;
                width: 128px;
                height: 82px;
            }

            .coin {
                top: 184px;
                left: 220px;
                width: 90px;
                height: 90px;
            }

            .cash {
                top: 234px;
                right: 30px;
                width: 72px;
                height: 46px;
            }

            .wallet {
                left: 34px;
                bottom: 156px;
            }

            .detail-grid {
                margin-top: 420px;
            }
        }
    </style>
</head>
<body>
    @php
        $brandName = 'KUYA ALLEN TECH SOLUTIONS';
        $brandLogoFile = file_exists(public_path('images/kuya-allen-logo.png'))
            ? 'images/kuya-allen-logo.png'
            : (file_exists(public_path('images/wowlogo.png')) ? 'images/wowlogo.png' : 'icons/icon.svg');
        $brandLogo = asset($brandLogoFile).'?v='.(file_exists(public_path($brandLogoFile)) ? filemtime(public_path($brandLogoFile)) : time());
    @endphp

    <main class="shell">
        <section class="hero">
            <nav class="nav">
                <div class="brand brand-with-logo">
                    <a href="{{ route('logo.viewer') }}" aria-label="View {{ $brandName }} logo">
                        <img class="marketing-brand-logo" src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
                    </a>
                    <a href="{{ route('welcome') }}">
                        <span>{{ $brandName }}</span>
                    </a>
                </div>
                <div class="menu">
                    <a href="{{ route('welcome') }}" class="{{ request()->routeIs('welcome') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                    <a href="{{ route('contacts') }}" class="{{ request()->routeIs('contacts') ? 'active' : '' }}">Contacts</a>
                    <a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}">FAQ</a>
                </div>
            </nav>

            <div class="content">
                @if(session('success') || $errors->any())
                    <div class="marketing-notices">
                        @if(session('success'))
                            <div class="marketing-flash success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="marketing-flash error">
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                @yield('content')
            </div>
        </section>
    </main>
</body>
</html>
