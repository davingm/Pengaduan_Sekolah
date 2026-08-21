<!DOCTYPE html>
<html lang="id" class="h-full bg-black text-slate-900 antialiased">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengaduan Sekolah') - Layanan Aspirasi Terpadu</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen bg-black selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation -->
    <x-navbar />

    <!-- Flash Notifications / Toast -->
    <x-toast />

    <!-- Main Dynamic Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Floating 1-Click Demo Role Switcher -->
    <x-demo-role-switcher />

</body>
</html>
