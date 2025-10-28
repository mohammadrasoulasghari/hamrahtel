# 📊 سیستم مغایرت‌یاب (File Comparison System)

> سیستم مقایسه هوشمند فایل‌های Excel و CSV با استفاده از هوش مصنوعی

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## ✨ ویژگی‌های کلیدی

- ✅ آپلود و پردازش فایل‌های Excel (xlsx, xls) و CSV
- ✅ تبدیل خودکار فایل‌ها به JSON
- ✅ مقایسه هوشمند با استفاده از OpenRouter AI
- ✅ نمایش نتایج با فرمت HTML زیبا
- ✅ رابط کاربری فارسی و راست‌چین
- ✅ طراحی مدرن با Tailwind CSS

## 🚀 نصب و راه‌اندازی

### پیش‌نیازها

- PHP >= 8.3
- Composer
- Node.js & NPM

### مراحل نصب

1. **کلون کردن ریپوزیتوری**
```bash
git clone https://github.com/mohammadrasoulasghari/hamrahtel.git
cd hamrahtel
```

2. **نصب وابستگی‌ها**
```bash
composer install
npm install
```

3. **تنظیمات محیطی**
```bash
cp .env.example .env
php artisan key:generate
```

فایل `.env` را ویرایش کنید و API Key خود را از OpenRouter وارد کنید:
```env
OPENROUTER_API_KEY=your-api-key-here
OPENROUTER_MODEL=openai/gpt-4o-mini
```

4. **ساخت دیتابیس**
```bash
php artisan migrate
```

5. **ایجاد Symbolic Link برای Storage**
```bash
php artisan storage:link
```

6. **راه‌اندازی سرور**
```bash
php artisan serve
```

## 📖 نحوه استفاده

1. به آدرس `http://127.0.0.1:8000` مراجعه کنید
2. فایل Excel یا CSV اول را انتخاب کنید
3. فایل Excel یا CSV دوم را انتخاب کنید
4. توضیحات مقایسه را وارد کنید (اختیاری)
5. دکمه "شروع مغایرت‌گیری" را کلیک کنید
6. منتظر نتایج تحلیل AI بمانید

## 🏗️ ساختار پروژه

```
├── app/
│   ├── Http/Controllers/
│   │   └── ComparisonController.php    # کنترلر مقایسه
│   ├── Models/
│   │   └── Comparison.php              # مدل دیتابیس
│   └── Services/
│       └── OpenRouterService.php       # سرویس AI
├── config/
│   └── openrouter.php                  # تنظیمات OpenRouter
├── database/migrations/
│   └── create_comparisons_table.php    # ساختار جدول
├── public/
│   ├── css/style.css                   # استایل‌ها
│   └── js/
│       ├── script.js                   # منطق اصلی
│       └── components/                 # کامپوننت‌ها
├── resources/views/
│   ├── index.blade.php                 # صفحه اصلی
│   └── result.blade.php                # نمایش نتایج
└── routes/web.php                      # مسیرها
```

## 🔧 تکنولوژی‌ها

- **Backend**: Laravel 11, maatwebsite/excel
- **Frontend**: Tailwind CSS, Feather Icons
- **AI**: OpenRouter API
- **Database**: SQLite (قابل تغییر)

## 🔑 دریافت API Key

1. به [OpenRouter.ai](https://openrouter.ai) مراجعه کنید
2. ثبت‌نام کنید
3. API Key دریافت کنید
4. در `.env` قرار دهید

## 📝 مستندات کامل

برای مستندات کامل به فایل [USAGE.md](USAGE.md) مراجعه کنید.

## 🤝 مشارکت

مشارکت‌ها همیشه خوش‌آمد هستند! لطفاً:

1. Fork کنید
2. یک branch جدید بسازید
3. تغییرات خود را commit کنید
4. Pull Request ارسال کنید

## 📄 لایسنس

این پروژه تحت لایسنس MIT منتشر شده است.

## 👨‍💻 توسعه‌دهنده

**Mohammad Rasoul Asghari**
- GitHub: [@mohammadrasoulasghari](https://github.com/mohammadrasoulasghari)

---

<p align="center">ساخته شده با ❤️ در ایران</p>

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
