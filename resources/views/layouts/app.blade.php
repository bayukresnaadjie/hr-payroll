<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>HR Payroll — @yield('title','Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="w-64 bg-indigo-950 flex flex-col fixed inset-y-0 left-0 z-30">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-indigo-900">
            <div class="w-9 h-9 bg-indigo-500 rounded-xl flex items-center justify-center flex-shrink-0 text-lg">👥</div>
            <div>
                <div class="font-bold text-white text-sm">HR Payroll</div>
                <div class="text-xs text-indigo-400">Sistem Manajemen SDM</div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-3 overflow-y-auto space-y-0.5">
            @php
            $menu = [
                ['route'=>'dashboard',          'label'=>'Dashboard',        'icon'=>'📊'],
                ['divider'=>'KARYAWAN'],
                ['route'=>'karyawan.index',     'label'=>'Data Karyawan',    'icon'=>'👤'],
                ['route'=>'departemen.index',   'label'=>'Departemen',       'icon'=>'🏢'],
                ['divider'=>'KEHADIRAN'],
                ['route'=>'absensi.index',      'label'=>'Absensi Harian',   'icon'=>'📅'],
                ['route'=>'absensi.rekap',      'label'=>'Rekap Absensi',    'icon'=>'📋'],
                ['divider'=>'PENGAJUAN'],
                ['route'=>'cuti.index',         'label'=>'Cuti',             'icon'=>'🏖️'],
                ['route'=>'lembur.index',       'label'=>'Lembur',           'icon'=>'⏰'],
                ['divider'=>'PENGGAJIAN'],
                ['route'=>'penggajian.index',   'label'=>'Periode Gaji',     'icon'=>'💰'],
                ['divider'=>'LAPORAN'],
                ['route'=>'laporan.penggajian', 'label'=>'Lap. Penggajian',  'icon'=>'📈'],
                ['route'=>'laporan.absensi',    'label'=>'Lap. Absensi',     'icon'=>'📉'],
                ['route'=>'laporan.karyawan',   'label'=>'Lap. Karyawan',    'icon'=>'📄'],
            ];
            @endphp

            @foreach($menu as $item)
                @if(isset($item['divider']))
                    <div class="pt-3 pb-1 px-3"><span class="text-xs font-semibold text-indigo-500 tracking-wider">{{ $item['divider'] }}</span></div>
                @else
                    @php $active = request()->routeIs(explode('.', $item['route'])[0] . '.*') || request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition {{ $active ? 'bg-indigo-600 text-white' : 'text-indigo-300 hover:bg-indigo-900 hover:text-white' }}">
                        <span class="text-base">{{ $item['icon'] }}</span>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="px-4 py-3 border-t border-indigo-900">
            <div class="flex items-center gap-2 px-2 mb-2">
                <div class="w-7 h-7 bg-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold text-xs">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-medium text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-indigo-400 capitalize">{{ str_replace('_',' ', auth()->user()->role) }}</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full flex items-center gap-2 px-3 py-1.5 text-xs text-indigo-400 hover:text-red-400 hover:bg-indigo-900 rounded-lg transition">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="flex-1 ml-64">
        <header class="bg-white border-b border-gray-200 px-8 py-4 sticky top-0 z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-base font-semibold text-gray-900">@yield('title','Dashboard')</h1>
                    <p class="text-xs text-gray-400">@yield('subtitle', now()->translatedFormat('l, d F Y'))</p>
                </div>
                <div class="flex items-center gap-3">@yield('header-action')</div>
            </div>
        </header>

        <div class="px-8 pt-4">
            @foreach(['success'=>'green','error'=>'red','info'=>'blue','warning'=>'yellow'] as $type => $color)
            @if(session($type))
            <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-700 px-4 py-3 rounded-lg text-sm mb-4">
                {{ session($type) }}
            </div>
            @endif
            @endforeach
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm mb-4">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
        </div>

        <div class="px-8 py-5 pb-10">@yield('content')</div>
    </main>
</div>
</body>
</html>
