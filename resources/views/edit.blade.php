@extends('layouts.app')

@section('title', 'Редактировать мастер-класс')

@section('content')
<div class="row row--nogutter top-line">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form action="{{ route('cabinet.update', $masterClass->id) }}" method="POST">
                @csrf
                @method('PUT')
                <h2>Редактирование мастер-класса</h2>
                
                {{-- Блок общих ошибок валидации --}}
                @if($errors->any())
                    <div style="background:#f44336; color:#fff; padding:12px 15px; margin-bottom:20px; border-radius:5px;">
                        <strong style="display:block; margin-bottom:5px;">⚠️ Ошибки в форме:</strong>
                        <ul style="margin:0; padding-left:20px; list-style-type:disc;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                {{-- Описание --}}
                <div class="form-group">
                    <label for="description">Описание мастер-класса *</label>
                    <textarea 
                        name="description" 
                        id="description"
                        required 
                        minlength="10"
                        rows="5"
                        style="width:100%; padding:10px; border:2px solid #ccc; border-radius:5px; font-size:14px; box-sizing:border-box; @error('description') border-color:#f44336; @enderror">{{ old('description', $masterClass->description) }}</textarea>
                    
                    @error('description')
                        <span style="color:#f44336; font-size:13px; display:block; margin-top:5px;">{{ $message }}</span>
                    @enderror
                    
                    <small style="color:#666; display:block; margin-top:5px;">
                        Минимум 10 символов
                    </small>
                </div>
                
                {{-- Цена --}}
                <div class="form-group">
                    <label for="price">Стоимость (руб.) *</label>
                    <input 
                        type="number" 
                        name="price" 
                        id="price"
                        value="{{ old('price', $masterClass->price) }}" 
                        min="0" 
                        max="100000" 
                        step="0.01"
                        required
                        style="width:100%; padding:10px; border:2px solid #ccc; border-radius:5px; font-size:14px; box-sizing:border-box; @error('price') border-color:#f44336; @enderror">
                    
                    @error('price')
                        <span style="color:#f44336; font-size:13px; display:block; margin-top:5px;">{{ $message }}</span>
                    @enderror
                    
                    <small style="color:#666; display:block; margin-top:5px;">
                        От 0 до 100 000 ₽, допускаются копейки
                    </small>
                </div>
                
                {{-- Кнопки --}}
                <div class="form-group" style="margin-top:25px;">
                    <button type="submit" class="btn" style="background:#4caf50; color:#fff; padding:12px 25px; border:none; border-radius:5px; cursor:pointer; font-size:14px;">
                        Сохранить изменения
                    </button>
                    <a href="{{ route('cabinet.index') }}" style="margin-left:15px; color:#666; text-decoration:none;">
                        ← Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection