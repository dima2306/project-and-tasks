<!DOCTYPE html>
<html lang="ka" class="h-full bg-gray-100" x-data="themeSwitcher()" :class="{ 'dark': isDark }" x-init="init()">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>მინიმალური ადმინ პანელი</title>

    @vite(['resources/css/admin/admin.css', 'resources/js/admin/admin.js'])
</head>
<body class="h-full flex bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
@include('admin._partials.sidebar')

<!-- Main content -->
<div class="flex-1 flex flex-col">
    @include('admin._partials.header')

    @yield('content')
</div>
</body>
</html>
