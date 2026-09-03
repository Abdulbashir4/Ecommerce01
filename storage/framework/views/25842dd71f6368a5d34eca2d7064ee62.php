<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#071a2f">
    <title>Sign in | Optimum Biomedical</title>
    <style>
        :root{--navy:#061426;--navy2:#0a2038;--cyan:#22d3ee;--blue:#2563eb;--ink:#10233d;--muted:#64748b;--line:#dbe5ef}
        *{box-sizing:border-box} html{min-height:100%;background:#061426} body{margin:0;min-height:100vh;font-family:Inter,ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:var(--ink);background:#f7fafc}
        button,input{font:inherit}.login-shell{min-height:100vh;display:grid;grid-template-columns:minmax(0,1.05fr) minmax(420px,.95fr);overflow:hidden}
        .visual{position:relative;display:flex;align-items:center;justify-content:center;padding:56px;background:radial-gradient(circle at 15% 20%,rgba(34,211,238,.18),transparent 32%),radial-gradient(circle at 80% 75%,rgba(37,99,235,.24),transparent 35%),linear-gradient(145deg,#04111f,#0a2742 58%,#0b1730);color:#fff;isolation:isolate}
        .visual:before{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.035) 1px,transparent 1px);background-size:42px 42px;mask-image:linear-gradient(to bottom,black,transparent)}
        .orb{position:absolute;border-radius:999px;filter:blur(1px);pointer-events:none;animation:float 8s ease-in-out infinite}.orb.a{width:280px;height:280px;top:-80px;right:-60px;background:rgba(34,211,238,.13)}.orb.b{width:220px;height:220px;bottom:-70px;left:-50px;background:rgba(99,102,241,.16);animation-delay:-3s}
        .visual-inner{position:relative;z-index:2;width:min(600px,100%)}.brand{display:inline-flex;align-items:center;gap:12px;margin-bottom:70px;color:#fff;text-decoration:none}.brand-mark{display:grid;place-items:center;width:48px;height:48px;border:1px solid rgba(255,255,255,.18);border-radius:15px;background:linear-gradient(135deg,rgba(34,211,238,.2),rgba(37,99,235,.3));box-shadow:0 15px 40px rgba(0,0,0,.2)}.brand-mark svg{width:26px;height:26px}.brand strong{font-size:20px;letter-spacing:-.03em}.eyebrow{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid rgba(34,211,238,.22);border-radius:999px;background:rgba(34,211,238,.07);color:#a5f3fc;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}.dot{width:7px;height:7px;border-radius:50%;background:#22d3ee;box-shadow:0 0 16px #22d3ee}.visual h2{max-width:600px;margin:20px 0 16px;font-size:clamp(40px,4.5vw,64px);line-height:1.02;letter-spacing:-.055em}.visual p{max-width:510px;margin:0;color:#b8c9da;font-size:16px;line-height:1.8}.trust-row{display:flex;gap:12px;flex-wrap:wrap;margin-top:34px}.trust{display:flex;align-items:center;gap:9px;padding:11px 14px;border:1px solid rgba(255,255,255,.1);border-radius:13px;background:rgba(255,255,255,.045);color:#dbeafe;font-size:13px}.trust svg{width:16px;color:#67e8f9}
        .panel{display:flex;align-items:center;justify-content:center;padding:42px;background:linear-gradient(180deg,#fff,#f8fbff)}.card{width:min(450px,100%)}.top-actions{display:flex;justify-content:flex-end;margin-bottom:34px}.home-link{display:inline-flex;align-items:center;gap:8px;color:#64748b;text-decoration:none;font-size:13px;font-weight:700;transition:.2s}.home-link:hover{color:#0f172a;transform:translateX(-2px)}.heading h1{margin:0;color:#0b1f38;font-size:34px;letter-spacing:-.04em}.heading p{margin:9px 0 0;color:#64748b;font-size:14px;line-height:1.6}.heading{margin-bottom:28px}.alert{display:flex;gap:11px;padding:13px 14px;margin-bottom:18px;border:1px solid #fecaca;border-radius:14px;background:#fff5f5;color:#b42318;font-size:13px;line-height:1.5}.field{margin-top:18px}.field label{display:block;margin-bottom:8px;color:#243b53;font-size:13px;font-weight:800}.input-wrap{position:relative}.input{width:100%;height:54px;padding:0 48px 0 45px;border:1px solid var(--line);border-radius:15px;background:#fff;color:#10233d;outline:none;box-shadow:0 2px 5px rgba(15,23,42,.025);transition:border-color .2s,box-shadow .2s,transform .2s}.input::placeholder{color:#9aaabd}.input:hover{border-color:#b9c8d8}.input:focus{border-color:#38bdf8;box-shadow:0 0 0 4px rgba(56,189,248,.12);transform:translateY(-1px)}.leading{position:absolute;left:16px;top:50%;width:18px;height:18px;transform:translateY(-50%);color:#94a3b8;pointer-events:none}.toggle{position:absolute;right:10px;top:50%;transform:translateY(-50%);width:36px;height:36px;border:0;border-radius:10px;background:transparent;color:#64748b;cursor:pointer}.toggle:hover{background:#eef6fb;color:#0f5f85}.submit{position:relative;width:100%;height:56px;margin-top:24px;border:0;border-radius:15px;background:linear-gradient(110deg,#0e7490,#2563eb 55%,#4f46e5);color:#fff;font-weight:900;letter-spacing:.01em;cursor:pointer;overflow:hidden;box-shadow:0 14px 28px rgba(37,99,235,.22);transition:transform .2s,box-shadow .2s}.submit:before{content:"";position:absolute;inset:0;background:linear-gradient(110deg,transparent 25%,rgba(255,255,255,.22),transparent 75%);transform:translateX(-120%);transition:transform .6s}.submit:hover{transform:translateY(-2px);box-shadow:0 18px 34px rgba(37,99,235,.28)}.submit:hover:before{transform:translateX(120%)}.submit:active{transform:translateY(0)}.submit-content{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;gap:9px}.spinner{display:none;width:17px;height:17px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:spin .7s linear infinite}.form-note{margin-top:17px;text-align:center;color:#7b8b9d;font-size:12px}.register{margin-top:25px;padding-top:22px;border-top:1px solid #e7edf3;text-align:center;color:#66788c;font-size:13px}.register a{color:#0b6b8e;font-weight:900;text-decoration:none}.register a:hover{text-decoration:underline}.legal{margin-top:20px;text-align:center;color:#a0adba;font-size:11px;line-height:1.6}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}@keyframes float{0%,100%{transform:translate3d(0,0,0)}50%{transform:translate3d(0,-18px,0)}}@keyframes spin{to{transform:rotate(360deg)}}
        @media(max-width:900px){.login-shell{grid-template-columns:1fr}.visual{min-height:300px;padding:34px 25px;align-items:flex-end}.visual-inner{max-width:620px}.brand{margin-bottom:38px}.visual h2{font-size:40px}.visual p{font-size:14px}.trust-row{display:none}.panel{padding:34px 22px 44px}.top-actions{margin-bottom:25px}}
        @media(max-width:520px){.visual{min-height:265px}.visual h2{font-size:34px}.visual p{font-size:13px}.brand{margin-bottom:28px}.brand-mark{width:42px;height:42px}.panel{padding:28px 18px 36px}.heading h1{font-size:30px}}
        @media(prefers-reduced-motion:reduce){*,*:before,*:after{animation:none!important;transition:none!important}}
    </style>
</head>
<body>
<div class="login-shell">
    <!--  -->

    <main class="panel">
        <div class="card">
            <div class="top-actions"><a class="home-link" href="<?php echo e(url('/')); ?>" aria-label="Return to home"><span aria-hidden="true">←</span> Back to store</a></div>
            <div class="heading">
                <h1>Welcome back</h1>
                <p>Enter your phone number and password to access your account.</p>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
                <div class="alert" role="alert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 8v5M12 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    <div><?php echo e($errors->first()); ?></div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <form method="POST" action="<?php echo e(url('/login')); ?>" id="loginForm">
                <?php echo csrf_field(); ?>
                <div class="field">
                    <label for="phone">Phone number</label>
                    <div class="input-wrap">
                        <svg class="leading" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7.5 4h2l1 4-1.8 1.3a14.5 14.5 0 0 0 6 6L16 13.5l4 1v2c0 1.1-.9 2-2 2C10.3 18.5 5.5 13.7 5.5 6c0-1.1.9-2 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
                        <input class="input" id="phone" name="phone" type="tel" inputmode="tel" autocomplete="username" value="<?php echo e(old('phone')); ?>" placeholder="01XXXXXXXXX" required autofocus aria-label="Phone number">
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="leading" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 0 1 8 0v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <input class="input" id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required aria-label="Password">
                        <button class="toggle" type="button" id="togglePassword" aria-label="Show password" aria-pressed="false"><svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.7"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.7"/></svg></button>
                    </div>
                </div>

                <button class="submit" type="submit" id="submitButton">
                    <span class="submit-content"><span class="spinner" id="spinner"></span><span id="submitText">Sign in securely</span><span id="arrow">→</span></span>
                </button>
                <p class="form-note">Your credentials are submitted over this secure session.</p>
            </form>

            <div class="register">Don't have an account? <a href="<?php echo e(url('/register')); ?>">Create an account</a></div>
            <div class="legal">© <?php echo e(date('Y')); ?> Optimum Biomedical. All rights reserved.</div>
        </div>
    </main>
</div>
<script>
(function(){
    const form=document.getElementById('loginForm'), password=document.getElementById('password'), toggle=document.getElementById('togglePassword'), spinner=document.getElementById('spinner'), text=document.getElementById('submitText'), arrow=document.getElementById('arrow');
    toggle?.addEventListener('click',function(){const show=password.type==='password';password.type=show?'text':'password';toggle.setAttribute('aria-pressed',String(show));toggle.setAttribute('aria-label',show?'Hide password':'Show password');});
    form?.addEventListener('submit',function(){if(!form.checkValidity())return;spinner.style.display='inline-block';text.textContent='Signing you in…';arrow.style.display='none';});
})();
</script>
</body>
</html>
<?php /**PATH E:\Laravel Project\optimumbiomedical\resources\views/auth/login.blade.php ENDPATH**/ ?>