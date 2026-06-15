<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tazkiyah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-8 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Welcome Back</h1>
            <p class="text-gray-500 mt-2">Login to continue your spiritual journey</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-500 p-4 rounded-lg mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" name="email" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="you@example.com">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white font-bold py-3 rounded-lg hover:bg-indigo-700 transition duration-300">
                Login
            </button>
        </form>

        <p class="text-center text-gray-500 mt-6">
            Don't have an account? <a href="{{ url('/register') }}"
                class="text-indigo-600 font-medium hover:underline">Sign up</a>
        </p>
    </div>

</body>

</html>