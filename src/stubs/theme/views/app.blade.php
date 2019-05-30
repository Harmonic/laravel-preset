<!DOCTYPE html>
<html class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ mix('/css/main.css') }}" rel="stylesheet">
    <script src="{{ mix('/js/app.js') }}" defer></script>
    @routes
</head>
<body class="font-sans leading-none text-gray-800 antialiased">

@inertia

</body>
</html>
