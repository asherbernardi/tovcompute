<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Anson Analytics</title>
    <script src="https://unpkg.com/htmx.org@1.9.5" integrity="sha384-xcuj3WpfgjlKF+FXhSQFQ0ZNr39ln+hwjN3npfM9VBnUskLolQAcN80McRIVOPuO" crossorigin="anonymous"></script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        <!--<link rel="stylesheet" href="{{ asset('css/app.css') }}">-->
        <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
<header class="p-4 shadow-xl mb-10">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Logo Section (Left) -->
        <div class="flex items-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="Anson Analytics" class="h-20">
        </div>

        <!-- Menu Section (Right) -->
        <nav class="space-x-6">
            <a href="{{ url('companies') }}" class="hover:text-gray-400">Companies</a>
            <a href="{{ url('charities') }}" class="hover:text-gray-400">Charities</a>
            <a href="{{ url('lists') }}" class="hover:text-gray-400">Lists</a>
            <a href="{{ url('recommendations') }}" class="hover:text-gray-400">Recommendations</a>
            <a href="{{ route('research.scores') }}" class="hover:text-gray-400">Research</a>
            <a href="{{ url('manual') }}" class="hover:text-gray-400">Manual</a>
        </nav>
    </div>
</header>

    <main>
        @yield('content')
    </main>
    <footer>
        </footer>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
