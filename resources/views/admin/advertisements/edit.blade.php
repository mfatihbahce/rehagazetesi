@extends('layouts.admin')

@section('title', 'Reklam Düzenle')
@section('page-title', 'Reklam Düzenle')

@section('content')
<form action="{{ route('admin.advertisements.update', $advertisement) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('admin.advertisements._form')

    <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Güncelle</button>
        <a href="{{ route('admin.advertisements.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300">İptal</a>
    </div>
</form>
@endsection
