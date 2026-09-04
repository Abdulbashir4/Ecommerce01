<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#071a2f">
    <title>Create Account | Optimum Biomedical</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
<div class="relative isolate min-h-screen overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-blue-950">
    <div class="absolute -left-32 -top-32 h-80 w-80 rounded-full bg-cyan-400/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -right-24 h-96 w-96 rounded-full bg-indigo-500/15 blur-3xl"></div>

    <div class="relative mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-white/10 bg-white shadow-2xl shadow-black/30 lg:grid-cols-2">
            <section class="hidden bg-gradient-to-br from-cyan-950 via-blue-950 to-indigo-950 p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-12">
                <div>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-white no-underline">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl border border-cyan-300/20 bg-cyan-400/10 text-cyan-200 shadow-lg shadow-cyan-950/30"><i class="fa-solid fa-heart-pulse text-xl"></i></span>
                        <span class="text-lg font-black tracking-tight">Optimum Biomedical</span>
                    </a>
                    <div class="mt-24">
                        <span class="inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/10 px-3 py-1.5 text-xs font-extrabold uppercase tracking-[0.16em] text-cyan-200"><span class="h-1.5 w-1.5 rounded-full bg-cyan-300"></span> Customer account</span>
                        <h1 class="mt-6 text-4xl font-black leading-tight tracking-[-0.04em] xl:text-5xl">Everything you need for better care.</h1>
                        <p class="mt-5 max-w-md text-sm leading-7 text-slate-300">Create your account to manage orders, track purchases and enjoy a faster shopping experience.</p>
                    </div>
                </div>
                <div class="grid gap-3 xl:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><i class="fa-solid fa-shield-halved text-cyan-300"></i><p class="mt-2 text-xs font-bold text-slate-200">Secure account</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><i class="fa-solid fa-truck-fast text-cyan-300"></i><p class="mt-2 text-xs font-bold text-slate-200">Order tracking</p></div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><i class="fa-solid fa-headset text-cyan-300"></i><p class="mt-2 text-xs font-bold text-slate-200">Customer support</p></div>
                </div>
            </section>

            <main class="bg-white px-5 py-8 sm:px-10 sm:py-12">
                <div class="mx-auto max-w-md">
                    <div class="flex items-center justify-between">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-slate-900"><i class="fa-solid fa-arrow-left"></i> Store</a>
                        <a href="{{ url('/login') }}" class="text-sm font-extrabold text-blue-700 hover:text-blue-900">Sign in</a>
                    </div>

                    <div class="mt-10">
                        <p class="text-xs font-black uppercase tracking-[0.18em] text-cyan-700">Get started</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Create your account</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Use your details below to create a secure customer account.</p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                            <div class="flex items-start gap-3"><i class="fa-solid fa-circle-exclamation mt-0.5"></i><div class="space-y-1">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div></div>
                        </div>
                    @endif

                    <form method="POST" action="{{ url('/register') }}" class="mt-7 space-y-5" id="registerForm">
                        @csrf
                        <div>
                            <label for="name" class="mb-2 block text-sm font-extrabold text-slate-700">Full name</label>
                            <div class="relative"><i class="fa-regular fa-user pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required maxlength="150" placeholder="Your full name" class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10"></div>
                        </div>
                        <div>
                            <label for="phone" class="mb-2 block text-sm font-extrabold text-slate-700">Phone number</label>
                            <div class="relative"><i class="fa-solid fa-phone pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel" required maxlength="50" placeholder="01XXXXXXXXX" class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10"></div>
                        </div>
                        <div>
                            <label for="password" class="mb-2 block text-sm font-extrabold text-slate-700">Password</label>
                            <div class="relative"><i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="password" name="password" type="password" autocomplete="new-password" required minlength="8" placeholder="At least 8 characters" class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10"><button type="button" data-toggle-password="password" aria-label="Show password" class="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"><i class="fa-regular fa-eye"></i></button></div>
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-extrabold text-slate-700">Confirm password</label>
                            <div class="relative"><i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required minlength="8" placeholder="Repeat your password" class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-cyan-500 focus:bg-white focus:ring-4 focus:ring-cyan-500/10"><button type="button" data-toggle-password="password_confirmation" aria-label="Show password" class="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"><i class="fa-regular fa-eye"></i></button></div>
                        </div>
                        <button type="submit" class="group relative flex h-14 w-full items-center justify-center gap-2 overflow-hidden rounded-2xl bg-gradient-to-r from-cyan-700 via-blue-700 to-indigo-700 font-black text-white shadow-xl shadow-blue-700/20 transition hover:-translate-y-0.5 hover:shadow-2xl hover:shadow-blue-700/25 focus:outline-none focus:ring-4 focus:ring-blue-500/20 active:translate-y-0"><span class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition duration-700 group-hover:translate-x-full"></span><span class="relative"><i class="fa-solid fa-user-plus"></i> Create account</span></button>
                    </form>
                    <p class="mt-6 text-center text-xs leading-5 text-slate-400">By creating an account, you agree to use the store responsibly and keep your login information secure.</p>
                </div>
            </main>
        </div>
    </div>
</div>
<script>
document.querySelectorAll('[data-toggle-password]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.togglePassword);
        const icon = button.querySelector('i');
        const visible = input.type === 'text';
        input.type = visible ? 'password' : 'text';
        icon.className = visible ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
        button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
    });
});
</script>
</body>
</html>
