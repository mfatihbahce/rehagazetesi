@extends('layouts.admin')

@section('title', 'Yeni Reklam')
@section('page-title', 'Yeni Reklam Ekle')

@section('content')
<form action="{{ route('admin.advertisements.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    @include('admin.advertisements._form')

    <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">Kaydet</button>
        <a href="{{ route('admin.advertisements.index') }}" class="bg-gray-200 px-6 py-2 rounded-lg hover:bg-gray-300">İptal</a>
    </div>
</form>
@endsection
