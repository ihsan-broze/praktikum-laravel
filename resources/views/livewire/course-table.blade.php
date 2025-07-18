<div>
    <h2 class="text-xl font-bold mb-2">{{ $isEdit ? 'Edit Course' : 'Tambah Course' }}</h2>

    <form wire:submit.prevent="save" class="space-y-2">
        <input type="text" wire:model="title" placeholder="Title" class="w-full p-2 border" />
        @error('title') <span class="text-red-500">{{ $message }}</span> @enderror

        <textarea wire:model="description" placeholder="Description" class="w-full p-2 border"></textarea>
        @error('description') <span class="text-red-500">{{ $message }}</span> @enderror

        <input type="number" wire:model="price" placeholder="Price" class="w-full p-2 border" />
        @error('price') <span class="text-red-500">{{ $message }}</span> @enderror

        <button type="submit" class="bg-blue-500 text-white px-4 py-2">{{ $isEdit ? 'Update' : 'Simpan' }}</button>
        <button type="button" wire:click="resetForm" class="bg-gray-400 px-4 py-2">Reset</button>
    </form>

    @if (session()->has('message'))
        <div class="mt-3 text-green-600">
            {{ session('message') }}
        </div>
    @endif

    <h2 class="text-xl font-bold mt-5">List Course</h2>
    <table class="table-auto w-full mt-2">
        <thead>
            <tr>
                <th>Judul</th>
                <th>Deskripsi</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
            <tr>
                <td>{{ $course->title }}</td>
                <td>{{ $course->description }}</td>
                <td>{{ $course->price }}</td>
                <td>
                    <button wire:click="edit({{ $course->id }})" class="text-blue-600">Edit</button>
                    <button wire:click="delete({{ $course->id }})" class="text-red-600">Hapus</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
