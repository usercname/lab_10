# 🚀 CI/CD Pipeline — Ochumelie Ruchki (Мастер-классы)

## 📋 Обзор

Пайплайн автоматизирует тестирование, анализ кода и симуляцию деплоя Laravel-приложения «ОчУмелые ручки».

**Технологии:**
- 🐘 PHP 8.3 + Laravel 11
- 🧪 PHPUnit + Coverage (гейт ≥ 50%)
- 🔍 PHPStan/Larastan (уровень: max)
- 🎨 Laravel Pint (preset: laravel / PSR-12)
- 🐙 GitHub Actions
- 🗄️ SQLite in-memory для тестов

---

## 🔄 Триггеры пайплайна

Пайплайн запускается автоматически при:
- `push` в ветки: `main`, `dev`, `qa`
- `pull_request` в те же ветки

```yaml
on:
  push:
    branches: [dev, qa, main]
  pull_request:
    branches: [dev, qa, main]