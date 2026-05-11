<!-- resources/views/login/login.blade.php -->
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KUSUG-PA | Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-pattern {
            background-color: #f0fdf4;
            background-image: radial-gradient(circle at 10% 20%, rgba(22, 163, 74, 0.05) 0%, transparent 50%),
                              radial-gradient(circle at 90% 80%, rgba(22, 163, 74, 0.08) 0%, transparent 50%),
                              radial-gradient(circle at 50% 50%, rgba(22, 163, 74, 0.03) 0%, transparent 70%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="h-full bg-pattern" x-data="{ showPassword: false, loading: false }">
    <div class="min-h-full flex">
        <!-- Left Side - Decorative -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-700 via-primary-600 to-primary-800 relative overflow-hidden">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-72 h-72 bg-white/5 rounded-full animate-float"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-white/5 rounded-full animate-float-delayed"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-white/5 rounded-full animate-float"></div>
            </div>
            
            <!-- Content -->
            <div class="relative flex flex-col justify-center px-12 w-full">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl mb-6">
                        <i class="fas fa-seedling text-3xl text-white"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-white mb-4">KUSUG-PA</h1>
                    <p class="text-primary-100 text-lg leading-relaxed max-w-md">
                        Sugarcane Crop Management & Recording System for Planters Association
                    </p>
                </div>

                <!-- Features -->
                <div class="space-y-4 mt-8">
                    <div class="flex items-center space-x-3 text-white/90">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-chart-line text-sm"></i>
                        </div>
                        <span class="text-sm">Real-time crop production tracking</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/90">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-invoice text-sm"></i>
                        </div>
                        <span class="text-sm">Automated voucher generation</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/90">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-users text-sm"></i>
                        </div>
                        <span class="text-sm">Planter member management</span>
                    </div>
                </div>

                <!-- Footer Info -->
                <div class="absolute bottom-8 left-12 right-12">
                    <div class="flex items-center justify-between text-white/60 text-xs">
                        <span>&copy; {{ date('Y') }} KUSUG-PA</span>
                        <span>v1.0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-primary-100 rounded-2xl mb-4">
                        <i class="fas fa-seedling text-3xl text-primary-600"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">KUSUG-PA</h2>
                    <p class="mt-2 text-sm text-gray-500">Sugarcane Management System</p>
                </div>

                <!-- Desktop Title -->
                <div class="hidden lg:block mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome back!</h2>
                    <p class="mt-1 text-sm text-gray-500">Please sign in to your account to continue</p>
                </div>

                <!-- Alert Messages -->
                @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-100 rounded-xl p-4" x-data="{ show: true }" x-show="show">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-red-700">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-100 rounded-xl p-4" x-data="{ show: true }" x-show="show">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 mt-0.5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-green-400 hover:text-green-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                <!-- Login Form -->
                <form class="space-y-5" action="{{ route('postLogin') }}" method="POST" @submit="loading = true">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">Username</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                            </div>
                            <input id="username" name="username" type="text" required
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 hover:bg-white focus:bg-white"
                                   placeholder="Enter your username" value="{{ old('username') }}" autocomplete="username">
                        </div>
                        @error('username')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                            </div>
                            <input id="password" name="password" 
                                   :type="showPassword ? 'text' : 'password'"
                                   required
                                   class="block w-full pl-10 pr-12 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 bg-gray-50 hover:bg-white focus:bg-white"
                                   placeholder="Enter your password" autocomplete="current-password">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" @click="showPassword = !showPassword" 
                                        class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                                    <i class="fas text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center"><i class="fas fa-info-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox"
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded cursor-pointer">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="loading"
                            class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        <span x-show="!loading">Sign in</span>
                        <span x-show="loading" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Signing in...
                        </span>
                    </button>
                </form>

                <!-- Bottom Text -->
                <p class="mt-8 text-center text-xs text-gray-400">
                    KUSUG-PA Check and Voucher System &copy; {{ date('Y') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>