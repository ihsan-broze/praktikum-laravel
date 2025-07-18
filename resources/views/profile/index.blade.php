<x-app-layout>
    <h2>Profil {{ $user->name }}</h2>
    <p>Email: {{ $user->email }}</p>
    <p>Role: {{ $user->role }}</p>

    <h3>QRIS untuk Pembayaran:</h3>
    <div>{!! $qrisCode !!}</div>
</x-app-layout>
