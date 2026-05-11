<!-- resources/views/login/admin-login.blade.php -->
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>KUSUG-PA | System Administration</title>
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
                        },
                        admin: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'shake': 'shake 0.5s ease-in-out',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        shake: {
                            '0%, 100%': { transform: 'translateX(0)' },
                            '25%': { transform: 'translateX(-5px)' },
                            '75%': { transform: 'translateX(5px)' },
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-admin-pattern {
            background-color: #0f172a;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(71, 85, 105, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(71, 85, 105, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(100, 116, 139, 0.05) 0%, transparent 70%);
        }
        .input-admin {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(71, 85, 105, 0.4);
            color: #e2e8f0;
        }
        .input-admin:focus {
            border-color: #475569;
            box-shadow: 0 0 0 2px rgba(71, 85, 105, 0.3);
            outline: none;
        }
        .input-admin::placeholder {
            color: #64748b;
        }
        .glow-effect {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.15);
        }
    </style>
</head>
<body class="h-full bg-admin-pattern" x-data="{ 
    showPassword: false, 
    loading: false, 
    step: 1,
    securityKey: '',
    securityError: false,
    securityShake: false
}">
    <div class="min-h-full flex">
        <!-- Left Side - Decorative Admin Panel -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-admin-900 via-admin-800 to-admin-900 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0" style="background-image: linear-gradient(rgba(71, 85, 105, 0.3) 1px, transparent 1px), linear-gradient(90deg, rgba(71, 85, 105, 0.3) 1px, transparent 1px); background-size: 60px 60px;"></div>
            </div>
            
            <div class="absolute inset-0">
                <div class="absolute top-20 left-10 w-72 h-72 bg-admin-700/20 rounded-full animate-float"></div>
                <div class="absolute bottom-20 right-10 w-96 h-96 bg-admin-600/10 rounded-full animate-float-delayed"></div>
                <div class="absolute top-1/3 right-1/4 w-48 h-48 bg-admin-500/10 rounded-full animate-pulse-slow"></div>
            </div>
            
            <div class="relative flex flex-col justify-center px-12 w-full">
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-admin-700/50 backdrop-blur-sm rounded-2xl mb-6 border border-admin-600/30">
                        <i class="fas fa-shield-halved text-3xl text-admin-300"></i>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <h1 class="text-4xl font-bold text-white">Admin Panel</h1>
                        <span class="bg-red-500/20 text-red-400 text-xs font-bold px-2 py-1 rounded-md uppercase tracking-wider">Restricted</span>
                    </div>
                    <p class="text-admin-400 text-lg leading-relaxed max-w-md">
                        System Administration & Configuration Portal for KUSUG-PA
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-8">
                    <div class="bg-admin-800/50 backdrop-blur-sm rounded-xl p-4 border border-admin-700/30">
                        <i class="fas fa-sliders text-admin-400 text-xl mb-2"></i>
                        <p class="text-admin-300 text-sm font-medium">System Settings</p>
                        <p class="text-admin-500 text-xs mt-1">Configure application parameters</p>
                    </div>
                    <div class="bg-admin-800/50 backdrop-blur-sm rounded-xl p-4 border border-admin-700/30">
                        <i class="fas fa-users-gear text-admin-400 text-xl mb-2"></i>
                        <p class="text-admin-300 text-sm font-medium">User Management</p>
                        <p class="text-admin-500 text-xs mt-1">Manage roles & permissions</p>
                    </div>
                    <div class="bg-admin-800/50 backdrop-blur-sm rounded-xl p-4 border border-admin-700/30">
                        <i class="fas fa-database text-admin-400 text-xl mb-2"></i>
                        <p class="text-admin-300 text-sm font-medium">Data Control</p>
                        <p class="text-admin-500 text-xs mt-1">Backup & maintenance</p>
                    </div>
                    <div class="bg-admin-800/50 backdrop-blur-sm rounded-xl p-4 border border-admin-700/30">
                        <i class="fas fa-chart-simple text-admin-400 text-xl mb-2"></i>
                        <p class="text-admin-300 text-sm font-medium">Analytics</p>
                        <p class="text-admin-500 text-xs mt-1">Monitor system activity</p>
                    </div>
                </div>

                <div class="absolute bottom-8 left-12 right-12">
                    <div class="flex items-center justify-between text-admin-500 text-xs">
                        <span>&copy; {{ date('Y') }} KUSUG-PA Admin</span>
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            System Active
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Admin Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-admin-900">
            <div class="max-w-md w-full">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-admin-800 rounded-2xl mb-4 border border-admin-700">
                        <i class="fas fa-shield-halved text-3xl text-admin-300"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-white">Admin Panel</h2>
                    <p class="mt-2 text-sm text-admin-400">Restricted Access</p>
                </div>

                <!-- Desktop Title -->
                <div class="hidden lg:block mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-lock text-admin-400"></i>
                        <h2 class="text-2xl font-bold text-white">Administrator Access</h2>
                    </div>
                    <p class="mt-1 text-sm text-admin-400">Enter your admin credentials to continue</p>
                </div>

                <!-- Security Notice -->
                <div class="mb-6 bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4">
                    <div class="flex items-start gap-2">
                        <i class="fas fa-triangle-exclamation text-yellow-500 mt-0.5"></i>
                        <div>
                            <p class="text-yellow-500 text-sm font-medium">Secure Area</p>
                            <p class="text-yellow-500/70 text-xs mt-0.5">This area is monitored. Unauthorized access attempts are logged and reported.</p>
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                @if(session('admin_error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-xl p-4" x-data="{ show: true }" x-show="show">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-lock text-red-400 mt-0.5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm text-red-400">{{ session('admin_error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-6 bg-red-500/10 border border-red-500/30 rounded-xl p-4" x-data="{ show: true }" x-show="show">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            @foreach($errors->all() as $error)
                                <p class="text-sm text-red-400">{{ $error }}</p>
                            @endforeach
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-300">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                @endif

                <!-- Step 1: Security Key Verification -->
                <div x-show="step === 1" x-transition>
                    <form @submit.prevent="verifySecurityKey" class="space-y-5">
                        <div>
                            <label for="security_key" class="block text-sm font-medium text-admin-300 mb-1.5">Security Access Key</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-key text-admin-500 group-focus-within:text-blue-400 transition-colors"></i>
                                </div>
                                <input id="security_key" x-model="securityKey" type="password" required
                                       :class="securityError ? 'input-admin border-red-500 shake' : 'input-admin'"
                                       class="input-admin block w-full pl-10 pr-12 py-3 rounded-xl text-sm focus:outline-none transition-all duration-200"
                                       placeholder="Enter security access key" autocomplete="off">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <button type="button" @click="showPassword = !showPassword" 
                                            class="text-admin-500 hover:text-admin-300 transition-colors p-1">
                                        <i class="fas text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>
                            <p x-show="securityError" class="mt-1.5 text-xs text-red-400 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i> Invalid security key. Please try again.
                            </p>
                        </div>

                        <button type="submit" 
                                class="w-full flex items-center justify-center py-3 px-4 border border-admin-600 rounded-xl shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-admin-900 transition-all duration-200 glow-effect">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Verify Access Key
                        </button>
                    </form>
                </div>

                <!-- Step 2: Admin Credentials -->
                <div x-show="step === 2" x-transition>
                    <form class="space-y-5" action="{{ route('admin.postLogin') }}" method="POST" @submit="loading = true">
                        @csrf
                        <input type="hidden" name="security_key" :value="securityKey">

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-admin-300 mb-1.5">Admin Username</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-user-shield text-admin-500 group-focus-within:text-admin-300 transition-colors"></i>
                                </div>
                                <input id="username" name="username" type="text" required
                                       class="input-admin block w-full pl-10 pr-4 py-3 rounded-xl text-sm focus:outline-none transition-all duration-200"
                                       placeholder="Enter admin username" value="{{ old('username') }}" autocomplete="off">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-admin-300 mb-1.5">Admin Password</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-admin-500 group-focus-within:text-admin-300 transition-colors"></i>
                                </div>
                                <input id="password" name="password" 
                                       :type="showPassword ? 'text' : 'password'"
                                       required
                                       class="input-admin block w-full pl-10 pr-12 py-3 rounded-xl text-sm focus:outline-none transition-all duration-200"
                                       placeholder="Enter admin password" autocomplete="off">
                            </div>
                        </div>

                        <!-- Back to Security Key -->
                        <button type="button" @click="step = 1" 
                                class="text-admin-400 hover:text-admin-300 text-xs transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Back to security key
                        </button>

                        <!-- Submit Button -->
                        <button type="submit" 
                                :disabled="loading"
                                class="w-full flex items-center justify-center py-3 px-4 border border-admin-600 rounded-xl shadow-sm text-sm font-semibold text-white bg-admin-700 hover:bg-admin-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-admin-500 focus:ring-offset-admin-900 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-right-to-bracket mr-2"></i>
                            <span x-show="!loading">Access Admin Panel</span>
                            <span x-show="loading" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Authenticating...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- Security Key Steps Indicator -->
                <div class="mt-6 flex items-center justify-center gap-2">
                    <div :class="step === 1 ? 'bg-blue-500 w-8' : 'bg-admin-700 w-3'" class="h-1.5 rounded-full transition-all duration-300"></div>
                    <div :class="step === 2 ? 'bg-blue-500 w-8' : 'bg-admin-700 w-3'" class="h-1.5 rounded-full transition-all duration-300"></div>
                </div>

                <!-- Return Link -->
                <div class="mt-8 text-center">
                    <a href="{{ route('getLogin') }}" class="text-admin-500 hover:text-admin-300 text-xs transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Return to user login
                    </a>
                </div>

                <!-- Bottom Text -->
                <p class="mt-6 text-center text-xs text-admin-600">
                    KUSUG-PA Administration System &copy; {{ date('Y') }}<br>
                    <span class="text-admin-700">All access attempts are logged and monitored</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        function verifySecurityKey() {
            const validKey = '{{ config("app.admin_security_key", "KUSUG-ADMIN-2024") }}';
            
            if (this.securityKey === validKey) {
                this.securityError = false;
                this.step = 2;
            } else {
                this.securityError = true;
                this.securityShake = true;
                setTimeout(() => { this.securityShake = false; }, 500);
            }
        }
    </script>
</body>
</html>