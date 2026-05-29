@extends('layouts.app')

@section('title', 'Регистрация - ОчУмелые ручки')

@section('content')
<div class="row row--nogutter top-line">
    <div class="line"></div>
</div>
<div class="main">
    <div class="row">
        <div class="row--small">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <h2>Форма регистрации</h2>
                
                
                <div class="form-group">
                    <label>ФИО <span style="color:#f44336;">*</span></label>
                    <input type="text" name="full_name"  value="{{ old('full_name') }}" 
                           placeholder="Иванов Иван Иванович"
                           style="border-color: {{ $errors->has('full_name') ? '#f44336' : '#20416c' }};">
                    @error('full_name')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Email <span style="color:#f44336;">*</span></label>
                    <input type="email" name="email"  value="{{ old('email') }}" 
                           placeholder="example@mail.ru"
                           style="border-color: {{ $errors->has('email') ? '#f44336' : '#20416c' }};">
                    @error('email')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Пароль <span style="color:#f44336;">*</span></label>
                    <input type="password" name="password"  
                           style="border-color: {{ $errors->has('password') ? '#f44336' : '#20416c' }};">
                    @error('password')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Подтверждение пароля <span style="color:#f44336;">*</span></label>
                    <input type="password" name="password_confirmation"  
                           style="border-color: {{ $errors->has('password') ? '#f44336' : '#20416c' }};">
                    @error('password')
                        <div style="color:#f44336; font-size:12px; margin-top:5px;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label>Номер телефона <span style="color:#f44336;">*</span></label>
                    <input type="tel" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone', '+7 ') }}"
                        placeholder="+7 (___) ___-__-__"
                        maxlength="18">

                @error('phone')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror

                <script>
                const phoneInput = document.getElementById('phone');

                // При загрузке ставим +7
            if (!phoneInput.value || phoneInput.value === '+7') {
    phoneInput.value = '+7 ';
}

        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value;
    
            // Удаляем всё кроме цифр
            let digits = value.replace(/\D/g, '');
    
                // Если начинается с 8 -> меняем на 7
            if (digits.startsWith('8')) {
                digits = '7' + digits.slice(1);
            }
    
            // Если не начинается с 7 -> добавляем 7
         if (!digits.startsWith('7')) {
        digits = '7' + digits;
        }
    
    // Оставляем только 11 цифр (7 + 10 цифр номера)
    digits = digits.slice(0, 11);
    
    // Форматируем: +7 (999) 123-45-67
    let formatted = '+7';
    if (digits.length > 1) {
        formatted += ' (' + digits.slice(1, 4);
    }
    if (digits.length >= 5) {
        formatted += ') ' + digits.slice(4, 7);
    }
    if (digits.length >= 8) {
        formatted += '-' + digits.slice(7, 9);
    }
    if (digits.length >= 10) {
        formatted += '-' + digits.slice(9, 11);
    }
    
    e.target.value = formatted;
});

// Запрещаем удалять +7
phoneInput.addEventListener('keydown', function(e) {
    // Если пытается удалить +7
    if (this.value.length <= 3 && (e.key === 'Backspace' || e.key === 'Delete')) {
        e.preventDefault();
    }
});

phoneInput.addEventListener('focus', function() {
    if (this.value.length < 3) {
        this.value = '+7 ';
    }
});
</script>
                </div>
                
                <div class="form-group">
                    <button class="btn">Зарегистрироваться</button>
                </div>
                
                <p style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('login') }}">Уже есть аккаунт? Войти</a>
                </p>
            </form>
        </div>
    </div>
</div>
@endsection