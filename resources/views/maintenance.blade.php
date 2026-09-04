<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $siteName }} — Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <main class="flex min-h-screen items-center justify-center px-5 py-12">
        <div class="w-full max-w-xl text-center">
            <div class="mx-auto grid h-20 w-20 place-items-center rounded-3xl bg-white/10 text-4xl">⚙️</div>
            <p class="mt-8 text-xs font-black uppercase tracking-[0.25em] text-cyan-300">Temporarily unavailable</p>
            <h1 class="mt-3 text-4xl font-black sm:text-5xl">We’ll be back soon</h1>
            <p class="mx-auto mt-5 max-w-lg text-sm leading-7 text-slate-300">{{ $message }}</p>
            <a href="{{ url('/login') }}" class="mt-8 inline-flex rounded-2xl bg-white px-6 py-3 text-sm font-black text-slate-950">Admin Login</a>
        </div>
    </main>
</body>
</html>
