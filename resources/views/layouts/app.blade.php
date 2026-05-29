<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Клуб любителей творчества «ОчУмелые ручки»')</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    
    <style>
    .auth {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .auth .user-name {
        font-weight: 500;
        margin-left: 15px;
        line-height: 1.2;
    }
    
    /* Разделитель */
    .auth span + a::before,
    .auth a + a::before {
        content: '|';
        color: #999;
        display: inline-block;
        vertical-align: middle;
        line-height: 1;
        margin-right: 10px;
    }
    
    /* Убираем все лишние псевдоэлементы */
    .auth *::before,
    .auth *::after {
        margin: 0;
        padding: 0;
    }
    
    .auth .user-name::before,
    .auth .user-name::after {
        content: none !important;
        display: none !important;
    }

    
</style>
</head>

<body class="@yield('body_class')">
    <div class="header">
        <div class="row grid middle between">
            <div class="logo">
                <a href="{{ route('home') }}" style="display: inline-block;">
                    <img src="{{ asset('img/logo.png') }}" alt="Логотип">
                </a>
            </div>
            <div class="title">
                Клуб любителей творчества «ОчУмелые ручки»
            </div>
            <div class="auth">
                @guest
                    <a href="{{ route('login') }}">Вход</a>
                @else
                    <span class="user-name">{{ auth()->user()->full_name }}</span>
                    @if(auth()->user()->isInstructor())
                        <a href="{{ route('cabinet.index') }}">ЛК</a>
                    @endif
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выход</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
                @endguest
            </div>
        </div>
    </div>

    <div class="row row--nogutter">
        <div class="menu-burger">
            <div class="burger">
                <div></div>
                <div></div>
                <div></div>
            </div>
        </div>
    </div>

    @if(session('message'))
        <div style="background:#4caf50; color:#fff; padding:10px; text-align:center;">{{ session('message') }}</div>
    @endif
    @if(session('error'))
        <div style="background:#f44336; color:#fff; padding:10px; text-align:center;">{{ session('error') }}</div>
    @endif

    <div class="main">
        @yield('content')
    </div>

    <div class="row row--nogutter">
        <div class="line"></div>
    </div>

    <div class="footer">
        <div class="row">
            <div class="row--small grid between">
                <div class="address">Наш адрес: ВДНХ, 120в</div>
                <div class="tel">Тел: 89123456765</div>
                <div class="copy">(с) Copyright, 2017</div>
            </div>
        </div>
    </div>

</body>
</html>