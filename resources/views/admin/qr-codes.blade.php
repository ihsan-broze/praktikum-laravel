<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User QR Codes - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                            ← Back to Dashboard
                        </a>
                        <h1 class="text-xl font-semibold text-gray-900">User QR Codes</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700">Welcome, {{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">User QR Codes</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">View QR codes generated for users.</p>
                </div>
                
                <div class="border-t border-gray-200">
                    @forelse($usersWithQR as $user)
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if($user->hasQrCode())
                                <div class="text-center">
                                    <img src="{{ $user->getQrCodeUrl() }}" alt="QR Code for {{ $user->name }}" 
                                         class="w-20 h-20 border border-gray-300 rounded">
                                    <a href="{{ $user->getQrCodeUrl() }}" target="_blank" 
                                       class="text-xs text-blue-600 hover:text-blue-800 mt-1 block">
                                        View Full Size
                                    </a>
                                </div>
                                @else
                                <span class="text-gray-400 text-sm">No QR Code</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-4 text-center text-gray-500">
                        No QR codes have been generated yet.
                    </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($usersWithQR->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $usersWithQR->links() }}
                </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>