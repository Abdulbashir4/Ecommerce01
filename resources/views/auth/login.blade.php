@php
    $siteName = \App\Models\Setting::get('general.site_name', 'Optimum Biomedical');
    $company = class_exists('App\\Models\\Company') ? \App\Models\Company::query()->first() : null;
    $siteName = $company?->company_name ?: $siteName;
    $logo = $company?->logo;
    $logoPath = $logo ? ltrim(str_replace('\\', '/', (string) $logo), '/') : null;
    $logoUrl = $logoPath
        ? (filter_var($logoPath, FILTER_VALIDATE_URL)
            ? $logoPath
            : (str_starts_with($logoPath, 'uploads/') || str_starts_with($logoPath, 'storage/')
                ? asset($logoPath)
                : asset('uploads/side_image/' . basename($logoPath))))
        : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#071b34">
    <title>Login | {{ $siteName }}</title>
    <style>
        :root{
            --navy:#071b34;--navy-2:#0c2d4f;--blue:#2563eb;--cyan:#06b6d4;
            --yellow:#facc15;--ink:#10233d;--muted:#64748b;--line:#dce6f0;
            --surface:#f7fafc;--white:#fff;
        }
        *{box-sizing:border-box}
        html{min-height:100%;background:var(--navy)}
        body{margin:0;min-height:100vh;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--ink);background:var(--surface)}
        a{text-decoration:none}button,input{font:inherit}
        .page{min-height:100vh;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(430px,.92fr);overflow:hidden}

        /* Brand side */
        .brand-side{position:relative;isolation:isolate;display:flex;align-items:center;padding:clamp(38px,6vw,88px);color:#fff;background:
            radial-gradient(circle at 18% 18%,rgba(6,182,212,.25),transparent 29%),
            radial-gradient(circle at 88% 80%,rgba(37,99,235,.32),transparent 35%),
            linear-gradient(145deg,#041222 0%,#082746 54%,#071b34 100%)}
        .brand-side:before{content:"";position:absolute;inset:0;z-index:-1;opacity:.75;background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);background-size:44px 44px;mask-image:linear-gradient(to bottom,#000,transparent 92%)}
        .brand-side:after{content:"";position:absolute;z-index:-1;width:430px;height:430px;right:-180px;top:-180px;border:1px solid rgba(103,232,249,.16);border-radius:50%;box-shadow:0 0 0 55px rgba(103,232,249,.025),0 0 0 110px rgba(103,232,249,.018)}
        .orb{position:absolute;border-radius:50%;pointer-events:none;filter:blur(1px);animation:float 8s ease-in-out infinite}.orb.one{width:260px;height:260px;left:-110px;bottom:-110px;background:rgba(37,99,235,.18)}.orb.two{width:150px;height:150px;right:10%;bottom:12%;background:rgba(6,182,212,.1);animation-delay:-3s}
        .brand-inner{width:min(650px,100%);position:relative;z-index:2}
        .brand-link{display:inline-flex;align-items:center;gap:13px;color:#fff;margin-bottom:clamp(45px,8vh,88px)}
        .logo-box{width:58px;height:58px;display:grid;place-items:center;overflow:hidden;border:1px solid rgba(255,255,255,.18);border-radius:17px;background:rgba(255,255,255,.08);box-shadow:0 18px 45px rgba(0,0,0,.2);backdrop-filter:blur(10px)}
        .logo-box img{width:100%;height:100%;padding:6px;object-fit:contain;background:#fff;border-radius:16px}.logo-fallback{font-size:23px;font-weight:950;letter-spacing:-.05em}.brand-name{font-size:19px;font-weight:900;letter-spacing:-.03em}.brand-sub{display:block;margin-top:2px;color:#9fb4c9;font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase}
        .pill{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(103,232,249,.2);border-radius:999px;background:rgba(6,182,212,.07);color:#a5f3fc;font-size:11px;font-weight:900;letter-spacing:.11em;text-transform:uppercase}.status-dot{width:7px;height:7px;border-radius:50%;background:#22d3ee;box-shadow:0 0 15px #22d3ee}
        .brand-title{margin:20px 0 17px;max-width:650px;font-size:clamp(40px,5vw,70px);line-height:1.02;letter-spacing:-.06em}.brand-copy{max-width:550px;margin:0;color:#b8c9da;font-size:16px;line-height:1.85}
        .feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:38px;max-width:610px}.feature{padding:15px;border:1px solid rgba(255,255,255,.1);border-radius:17px;background:rgba(255,255,255,.045);backdrop-filter:blur(8px)}.feature-icon{width:30px;height:30px;display:grid;place-items:center;border-radius:9px;background:rgba(34,211,238,.1);color:#67e8f9;margin-bottom:11px}.feature strong{display:block;font-size:12px}.feature span{display:block;margin-top:4px;color:#9fb4c9;font-size:10px;line-height:1.5}

        /* Form side */
        .form-side{display:flex;align-items:center;justify-content:center;padding:34px 7%;background:linear-gradient(180deg,#fff 0%,#f8fbff 100%)}.form-wrap{width:min(430px,100%)}
        .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:38px}.mobile-brand{display:none;align-items:center;gap:9px;color:var(--ink);font-weight:900}.mobile-brand .mini-logo{width:38px;height:38px;border-radius:11px;object-fit:contain;border:1px solid #e4ebf2}.home-link{display:inline-flex;align-items:center;gap:7px;margin-left:auto;color:#718198;font-size:12px;font-weight:800;transition:.2s}.home-link:hover{color:#0f172a;transform:translateX(-2px)}
        .heading{margin-bottom:28px}.heading h1{margin:0;color:#091d36;font-size:36px;line-height:1.1;letter-spacing:-.045em}.heading p{margin:10px 0 0;color:#718198;font-size:13px;line-height:1.7}
        .alert{display:flex;align-items:flex-start;gap:10px;margin-bottom:19px;padding:13px 14px;border:1px solid #fecaca;border-radius:14px;background:#fff6f6;color:#b42318;font-size:12px;line-height:1.55}.alert svg{flex:0 0 auto;margin-top:1px}
        .field{margin-top:18px}.field-label{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}.field-label label{color:#233b55;font-size:12px;font-weight:900;letter-spacing:.01em}.field-label span{color:#a0aec0;font-size:10px}
        .input-wrap{position:relative}.input{width:100%;height:56px;padding:0 47px 0 47px;border:1px solid var(--line);border-radius:15px;outline:none;background:#fff;color:var(--ink);box-shadow:0 2px 6px rgba(15,23,42,.025);transition:border-color .2s,box-shadow .2s,transform .2s}.input::placeholder{color:#a1afbf}.input:hover{border-color:#c4d1df}.input:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(56,189,248,.12);transform:translateY(-1px)}.leading{position:absolute;left:16px;top:50%;width:18px;height:18px;transform:translateY(-50%);color:#91a1b3;pointer-events:none}.toggle{position:absolute;right:9px;top:50%;width:37px;height:37px;display:grid;place-items:center;transform:translateY(-50%);border:0;border-radius:10px;background:transparent;color:#718198;cursor:pointer}.toggle:hover{background:#eef7fb;color:#0b638b}
        .options{display:flex;justify-content:flex-end;align-items:center;margin-top:10px}.forgot{font-size:11px;color:#2563eb;font-weight:850}.forgot:hover{text-decoration:underline}
        .submit{position:relative;width:100%;height:56px;margin-top:24px;border:0;border-radius:15px;overflow:hidden;background:linear-gradient(110deg,#0e7490,#2563eb 55%,#4f46e5);color:#fff;cursor:pointer;font-weight:900;box-shadow:0 15px 30px rgba(37,99,235,.22);transition:transform .2s,box-shadow .2s}.submit:before{content:"";position:absolute;inset:0;background:linear-gradient(110deg,transparent 20%,rgba(255,255,255,.2),transparent 80%);transform:translateX(-120%);transition:transform .65s}.submit:hover{transform:translateY(-2px);box-shadow:0 20px 38px rgba(37,99,235,.28)}.submit:hover:before{transform:translateX(120%)}.submit:active{transform:translateY(0)}.submit-content{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;gap:9px}.spinner{display:none;width:17px;height:17px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}.arrow{font-size:19px;line-height:1}
        .secure-note{display:flex;justify-content:center;align-items:center;gap:6px;margin-top:15px;color:#8a98a9;font-size:10px}.secure-note svg{color:#22a06b}
        .register{margin-top:25px;padding-top:22px;border-top:1px solid #e8edf3;text-align:center;color:#718198;font-size:12px}.register a{color:#0b6b8e;font-weight:900}.register a:hover{text-decoration:underline}.copyright{margin-top:19px;text-align:center;color:#a2adba;font-size:10px}
        @keyframes float{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(0,-17px,0)}}@keyframes spin{to{transform:rotate(360deg)}}

        @media(max-width:1050px){.page{grid-template-columns:minmax(0,1fr) minmax(390px,.9fr)}.brand-side{padding:48px}.feature-grid{grid-template-columns:1fr}.feature:nth-child(3){display:none}.brand-title{font-size:clamp(40px,5vw,58px)}}
        @media(max-width:820px){.page{display:block;min-height:100vh}.brand-side{min-height:320px;padding:32px 24px 38px;align-items:flex-end}.brand-link{margin-bottom:30px}.brand-title{font-size:39px;margin:15px 0 10px}.brand-copy{font-size:13px;line-height:1.65}.feature-grid{display:none}.form-side{min-height:calc(100vh - 320px);padding:32px 24px 44px}.mobile-brand{display:flex}.topbar{margin-bottom:27px}.topbar .home-link{font-size:11px}.form-wrap{width:min(440px,100%)}}
        @media(max-width:520px){.brand-side{min-height:285px;padding:25px 18px 30px}.brand-link{margin-bottom:24px}.logo-box{width:48px;height:48px;border-radius:14px}.brand-name{font-size:16px}.brand-sub{font-size:9px}.brand-title{font-size:31px;letter-spacing:-.05em}.brand-copy{font-size:12px}.form-side{padding:26px 18px 36px}.topbar{margin-bottom:25px}.heading h1{font-size:31px}.heading p{font-size:12px}.input{height:54px}.submit{height:54px}}
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{animation:none!important;transition:none!important}}
    </style>
</head>
<body>
<div class="page">
    <section class="brand-side" aria-label="{{ $siteName }}">
        <span class="orb one"></span><span class="orb two"></span>
        <div class="brand-inner">
            <a href="{{ url('/') }}" class="brand-link">
                <span class="logo-box">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }} logo" onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                        <span class="logo-fallback" style="display:none">OB</span>
                    @else
                        <span class="logo-fallback">OB</span>
                    @endif
                </span>
                <span><span class="brand-name">{{ $siteName }}</span><span class="brand-sub">Biomedical &amp; Healthcare</span></span>
            </a>

            <div class="pill"><span class="status-dot"></span> Customer portal</div>
            <h2 class="brand-title">Everything you need.<br>One secure account.</h2>
            <p class="brand-copy">Sign in to manage your orders, track purchases, save products and enjoy a faster checkout experience.</p>

            <div class="feature-grid">
                <div class="feature"><div class="feature-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.5 2.9 8.4 7 10 4.1-1.6 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.8"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></div><strong>Secure access</strong><span>Your account stays protected.</span></div>
                <div class="feature"><div class="feature-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 7h16v12H4zM8 7V5h8v2" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 12h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div><strong>Easy ordering</strong><span>Find products and checkout faster.</span></div>
                <div class="feature"><div class="feature-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 4h12v16l-6-3-6 3V4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 11h6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></div><strong>Saved account</strong><span>Keep your details ready for next time.</span></div>
            </div>
        </div>
    </section>

    <main class="form-side">
        <div class="form-wrap">
            <div class="topbar">
                <a href="{{ url('/') }}" class="mobile-brand">
                    @if($logoUrl)<img class="mini-logo" src="{{ $logoUrl }}" alt="{{ $siteName }}">@endif
                    <span>{{ $siteName }}</span>
                </a>
                <a class="home-link" href="{{ url('/') }}"><span aria-hidden="true">←</span> Back to store</a>
            </div>

            <div class="heading">
                <h1>Welcome back</h1>
                <p>Enter your phone number and password to continue to your account.</p>
            </div>

            @if ($errors->any())
                <div class="alert" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <div>{{ $errors->first() }}</div>
                </div>
            @endif

            <form method="POST" action="{{ url('/login') }}" id="loginForm">
                @csrf

                <div class="field">
                    <div class="field-label"><label for="phone">Phone number</label><span>Required</span></div>
                    <div class="input-wrap">
                        <svg class="leading" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7.5 4h2l1 4-1.8 1.3a14.5 14.5 0 0 0 6 6L16 13.5l4 1v2c0 1.1-.9 2-2 2C10.3 18.5 5.5 13.7 5.5 6c0-1.1.9-2 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        <input class="input" id="phone" name="phone" type="tel" inputmode="tel" autocomplete="username" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" required autofocus>
                    </div>
                </div>

                <div class="field">
                    <div class="field-label"><label for="password">Password</label><span>Required</span></div>
                    <div class="input-wrap">
                        <svg class="leading" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <input class="input" id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
                        <button class="toggle" type="button" id="togglePassword" aria-label="Show password" aria-pressed="false">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg>
                        </button>
                    </div>
                </div>

                <div class="options"><a class="forgot" href="{{ url('/forgot-password') }}">Forgot password?</a></div>

                <button class="submit" type="submit" id="submitButton">
                    <span class="submit-content"><span class="spinner" id="spinner"></span><span id="submitText">Sign in securely</span><span class="arrow" id="arrow">→</span></span>
                </button>

                <div class="secure-note"><svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.5 2.9 8.4 7 10 4.1-1.6 7-5.5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.8"/><path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg> Your login details are protected during this session.</div>
            </form>

            <div class="register">Don't have an account? <a href="{{ url('/register') }}">Create an account</a></div>
            <div class="copyright">© {{ date('Y') }} {{ $siteName }}. All rights reserved.</div>
        </div>
    </main>
</div>

<script>
(function(){
    const form=document.getElementById('loginForm');
    const password=document.getElementById('password');
    const toggle=document.getElementById('togglePassword');
    const spinner=document.getElementById('spinner');
    const text=document.getElementById('submitText');
    const arrow=document.getElementById('arrow');

    toggle?.addEventListener('click',function(){
        const show=password.type==='password';
        password.type=show?'text':'password';
        toggle.setAttribute('aria-pressed',String(show));
        toggle.setAttribute('aria-label',show?'Hide password':'Show password');
    });

    form?.addEventListener('submit',function(){
        if(!form.checkValidity()) return;
        spinner.style.display='inline-block';
        text.textContent='Signing you in…';
        arrow.style.display='none';
    });
})();
</script>
</body>
</html>
