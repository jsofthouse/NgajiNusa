<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Title & Description (SEO) -->
    <title>NgajiNusa - Kursus Ngaji Online Bersama Guru Bersertifikat</title>
    <meta name="description"
        content="Belajar ngaji online mudah & fleksibel bersama 50+ guru bersertifikat. Tahsin, tajwid, hafalan via Zoom. Pantau progres belajar real-time. Daftar sekarang!">
    <meta name="keywords"
        content="belajar ngaji online, kursus ngaji, les ngaji online, tahsin online, tajwid online, hafalan quran online, guru ngaji online, ngaji via zoom">
    <meta name="robots" content="index, follow">
    <meta name="author" content="NgajiNusa">
    <link rel="canonical" href="https://ngajinusa.com/">

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn preview) -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ngajinusa.com/">
    <meta property="og:site_name" content="NgajiNusa">
    <meta property="og:title" content="NgajiNusa - Kursus Ngaji Online Bersama Guru Bersertifikat">
    <meta property="og:description"
        content="Belajar ngaji online mudah & fleksibel bersama 50+ guru bersertifikat. Tahsin, tajwid, hafalan via Zoom. Pantau progres belajar real-time.">
    <meta property="og:image" content="https://ngajinusa.com/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="NgajiNusa - Platform Belajar Ngaji Online">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="NgajiNusa - Kursus Ngaji Online Bersama Guru Bersertifikat">
    <meta name="twitter:description"
        content="Belajar ngaji online mudah & fleksibel bersama 50+ guru bersertifikat. Tahsin, tajwid, hafalan via Zoom.">
    <meta name="twitter:image" content="https://ngajinusa.com/images/og-image.jpg">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Theme color (address bar mobile) -->
    <meta name="theme-color" content="#0f766e">

    {{-- <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "EducationalOrganization",
        "name": "NgajiNusa",
        "description": "Platform belajar ngaji online terpercaya dengan guru bersertifikat dan metode terstruktur.",
        "url": "https://ngajinusa.com",
        "logo": "https://ngajinusa.com/images/logo.png"
    }
    </script> --}}

    <!-- Font & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
    @stack('styles')
</head>

<body>

    @yield('content')

    @stack('scripts')
</body>

</html>
