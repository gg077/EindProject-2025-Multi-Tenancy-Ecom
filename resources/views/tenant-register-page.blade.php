<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Registreer subdomein</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md p-6 bg-white rounded shadow">
    {{-- Hier mount je Livewire-form --}}
    <livewire:auth.tenant-register />
</div>
</body>
</html>
