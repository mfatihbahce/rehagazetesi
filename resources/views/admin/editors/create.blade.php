@extends('layouts.admin')

@section('title', 'Yeni Editör')
@section('page-title', 'Yeni Editör Ekle')

@section('content')
<form action="{{ route('admin.editors.store') }}" method="POST" class="w-full">
    @csrf
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border rounded-lg px-4 py-2 @error('name') border-red-500 @enderror">
            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded-lg px-4 py-2 @error('email') border-red-500 @enderror">
            @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Şifre *</label>
            <input type="password" name="password" required class="w-full border rounded-lg px-4 py-2 @error('password') border-red-500 @enderror">
            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Şifre Tekrar *</label>
            <input type="password" name="password_confirmation" required class="w-full border rounded-lg px-4 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Unvan</label>
            <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded-lg px-4 py-2" placeholder="Örn: Muhabir, Editör">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Biyografi</label>
            <textarea name="bio" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('bio') }}</textarea>
        </div>
    </div>
    <div class="mt-6 flex gap-4">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Kaydet</button>
        <a href="{{ route('admin.editors.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300">İptal</a>
    </div>
</form>
@endsection
