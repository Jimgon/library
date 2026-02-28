<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SNHS Library System - Login</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: {
                            DEFAULT: "#eff6ff",
                            dark: "#1e3a8a"
                        },
                        card: {
                            DEFAULT: "#f8fafc",
                            dark: "#1e293b"
                        },
                        border: {
                            DEFAULT: "#bfdbfe",
                            dark: "#3b82f6"
                        },
                        primary: {
                            DEFAULT: "#93c5fd",
                            dark: "#60a5fa"
                        },
                        accent: {
                            DEFAULT: "#93c5fd",
                            dark: "#60a5fa"
                        },
                        muted: {
                            DEFAULT: "#1e40af",
                            dark: "#93c5fd"
                        }
                    },
                    fontFamily: {
                        sans: ['ui-sans-serif', 'system-ui'],
                        heading: ['Lora', 'serif']
                    }
                }
            }
        }
    </script>

    <script>
        // Dark mode persistence
        if (
            localStorage.theme === 'dark' ||
            (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
        ) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <style>
        .logo {
            width: 8rem;
            height: 8rem;
            object-fit: contain;
        }
        .submit-btn {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            transition: all .3s ease;
        }
        .submit-btn:hover {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            box-shadow: 0 8px 16px rgba(147,197,253,.3);
            transform: translateY(-1px);
        }

        .form-input {
            background: #ffffff;
            border: 1px solid #bfdbfe;
        }
        .form-input:focus {
            background: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .dark .form-input {
            background: #1e293b;
            border-color: #3b82f6;
            color: #fff;
        }
        .dark .form-input:focus {
            border-color: #528cff;
            box-shadow: 0 0 0 3px rgba(82,140,255,0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-background dark:bg-background-dark text-gray-900 dark:text-white font-sans">

    <!-- dark mode toggle -->
    <button onclick="toggleDarkMode()" class="fixed top-6 right-6 p-3 rounded-full bg-white dark:bg-gray-800 shadow-md" aria-label="toggle dark mode">
        <i class="fas fa-moon dark:hidden"></i>
        <i class="fas fa-sun hidden dark:inline"></i>
    </button>

    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="w-full max-w-md">
            <div class="bg-card dark:bg-card-dark rounded-2xl shadow-xl p-8">
                <div class="text-center mb-6">
                    <img src="{{ asset('images/snhs-logo.png') }}" alt="SNHS Logo" class="logo mx-auto">
                    <h1 class="mt-4 text-3xl font-heading font-bold text-dark">Library System</h1>
                    <p class="text-muted mt-1">Subic National High School</p>
                </div>
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border-l-4 border-red-500 bg-red-50
                                dark:border-red-700 dark:bg-red-950/30 px-5 py-4 text-sm text-red-700 dark:text-red-400">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle flex-shrink-0 mt-0.5"></i>
                            <ul class="space-y-1 flex-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-muted">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="form-input w-full rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-0 transition"
                            placeholder="you@example.com"
                        >
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-muted">Password</label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="form-input w-full rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-0 transition"
                            placeholder="Enter password"
                        >
                    </div>

                    <!-- Role -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-muted">Login as</label>
                        <div class="relative">
                            <select
                                name="role"
                                required
                                class="form-input w-full rounded-lg px-4 py-2 text-sm appearance-none focus:outline-none focus:ring-0 transition pr-10 cursor-pointer"
                            >
                                <option value="">Select role</option>
                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-primary"></i>
                        </div>
                    </div>

                    <button type="submit" class="submit-btn w-full rounded-lg px-4 py-2 text-white font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-muted">
                    <a href="{{ route('admin.create') }}" class="text-primary hover:underline">Create admin account</a>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleDarkMode() {
            const root = document.documentElement
            if (root.classList.contains('dark')) {
                root.classList.remove('dark')
                localStorage.theme = 'light'
            } else {
                root.classList.add('dark')
                localStorage.theme = 'dark'
            }
        }
    </script>

</body>
</html>
