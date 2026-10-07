<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login · HR Payroll</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-indigo-950 min-h-screen flex items-center justify-center p-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">👥</div>
        <h1 class="font-bold text-white text-2xl">HR Payroll</h1>
        <p class="text-indigo-400 text-sm mt-1">Sistem Manajemen SDM & Penggajian</p>
    </div>
    <div class="bg-white rounded-2xl shadow-xl p-7">
        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-4">
            {{ $errors->first() }}
        </div>
        @endif
        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       placeholder="nama@perusahaan.com"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"/>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-xl transition">
                Masuk ke Sistem
            </button>
        </form>
        <div class="mt-5 p-3 bg-gray-50 rounded-xl text-xs text-gray-500 space-y-1">
            <div class="font-semibold text-gray-600 mb-1">Akun Demo:</div>
            <div>👑 admin@hr.test — superadmin</div>
            <div>👤 hr@hr.test — hr_manager</div>
            <div>💰 finance@hr.test — finance</div>
            <div class="text-gray-400">Password: password</div>
        </div>
    </div>
</div>
</body>
</html>
