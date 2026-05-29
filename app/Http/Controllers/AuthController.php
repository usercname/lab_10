<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'email.required'    => 'Введите email',
            'email.email'       => 'Некорректный формат email',
            'email.max'         => 'Email слишком длинный',
            'password.required' => 'Введите пароль',
            'password.min'      => 'Пароль должен быть не короче 8 символов',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->isInstructor()) {
                return redirect()->intended(route('cabinet.index'))
                    ->with('message', 'Добро пожаловать в личный кабинет, ' . $user->full_name . '!');
            }

            return redirect()->intended(route('home'))
                ->with('message', 'Добро пожаловать, ' . $user->full_name . '!');
        }

        throw ValidationException::withMessages([
            'email' => 'Неверный email или пароль.',
        ]);
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[а-яА-ЯёЁa-zA-Z\s\-]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[\+]?[78]?[\s\-]?\(?[0-9]{3}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
                'unique:users,phone',
            ],
        ], [
            // ФИО
            'full_name.required' => 'Поле ФИО обязательно для заполнения.',
            'full_name.regex'    => 'ФИО может содержать только буквы, пробелы и дефисы.',
            'full_name.max'      => 'ФИО не может быть длиннее 255 символов.',

            // Email
            'email.required' => 'Введите email.',
            'email.email'    => 'Некорректный формат email.',
            'email.unique'   => 'Этот email уже занят.',
            'email.max'      => 'Email слишком длинный.',

            // Пароль
            'password.required'  => 'Введите пароль.',
            'password.confirmed' => 'Пароли не совпадают.',
            'password.min'       => 'Пароль должен быть не короче 8 символов.',

            // Телефон
            'phone.required' => 'Введите номер телефона.',
            'phone.regex'    => 'Некорректный формат телефона.',
            'phone.unique'   => 'Этот номер уже зарегистрирован.',
        ]);

        $user = User::create([
            'full_name' => trim($validated['full_name']),
            'email'     => strtolower(trim($validated['email'])),
            'password'  => bcrypt($validated['password']),
            'phone'     => $this->normalizePhone($validated['phone']),
            'role'      => 'visitor',
        ]);

        return redirect()->route('login')
            ->with('success', 'Регистрация успешна! Теперь вы можете войти.');
    }

    public function logout(Request $request)
    {
        $userName = Auth::user()->full_name ?? 'Пользователь';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'До свидания, ' . $userName . '! Вы вышли из системы.');
    }

    /**
     * Нормализация номера телефона к формату +79991234567
     */
    private function normalizePhone($phone)
    {
        // Удаляем всё, кроме цифр и +
        $phone = preg_replace('/[^\d+]/', '', $phone);

        // Если начинается с 8 -> заменяем на 7
        if (str_starts_with($phone, '8')) {
            $phone = '7' . substr($phone, 1);
        }

        // Если начинается с +7 -> убираем плюс, оставляем 7
        if (str_starts_with($phone, '+7')) {
            $phone = substr($phone, 1);
        }

        // Если номер из 10 цифр (без кода страны) -> добавляем 7 в начало
        if (strlen($phone) === 10 && ctype_digit($phone)) {
            $phone = '7' . $phone;
        }

        // Возвращаем в формате +79991234567
        return '+' . $phone;
    }
}
