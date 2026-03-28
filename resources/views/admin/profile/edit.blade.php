@extends('layouts.admin')

@section('title', 'Profil Ayarları')
@section('page-title', 'Profil Ayarları')

@section('content')
<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="w-full">
    @csrf
    @method('PUT')
    <div class="bg-white rounded-xl shadow p-6 space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ad Soyad *</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full border rounded-lg px-4 py-2 @error('name') border-red-500 @enderror">
            @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">E-posta *</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded-lg px-4 py-2 @error('email') border-red-500 @enderror">
            @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre</label>
            <input type="password" name="password" class="w-full border rounded-lg px-4 py-2 @error('password') border-red-500 @enderror" placeholder="Değiştirmek istemiyorsanız boş bırakın">
            @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Yeni Şifre Tekrar</label>
            <input type="password" name="password_confirmation" class="w-full border rounded-lg px-4 py-2">
        </div>

        @if($user->isEditor())
        <div class="border-t border-gray-200 pt-6 space-y-4">
            <h3 class="font-semibold text-gray-800">Editör Profili</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Profil Fotoğrafı</label>
                @if($user->editorProfile?->profile_photo)
                <div class="mb-2"><img src="{{ asset('storage/'.$user->editorProfile->profile_photo) }}" alt="Profil" class="w-24 h-24 rounded-full object-cover"></div>
                @endif
                <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/gif,image/webp" class="w-full border rounded-lg px-4 py-2 text-sm">
                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF veya WebP. Boş bırakırsanız mevcut fotoğraf korunur.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unvan</label>
                <input type="text" name="title" value="{{ old('title', $user->editorProfile?->title) }}" class="w-full border rounded-lg px-4 py-2" placeholder="Örn: Muhabir, Editör">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Biyografi</label>
                <textarea name="bio" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('bio', $user->editorProfile?->bio) }}</textarea>
            </div>
        </div>
        @endif
    </div>
    <div class="mt-6">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Kaydet</button>
    </div>
</form>
@endsection
