# سیستم مغایرت‌یاب (File Comparison System)

سیستم مقایسه هوشمند فایل‌های Excel و CSV با استفاده از هوش مصنوعی

## ویژگی‌های کلیدی

- ✅ آپلود و پردازش فایل‌های Excel (xlsx, xls) و CSV
- ✅ تبدیل خودکار فایل‌ها به JSON
- ✅ مقایسه هوشمند با استفاده از OpenRouter AI
- ✅ نمایش نتایج با فرمت HTML زیبا
- ✅ رابط کاربری فارسی و راست‌چین
- ✅ طراحی مدرن با Tailwind CSS

## نصب و راه‌اندازی

### 1. نصب وابستگی‌ها

```bash
composer install
npm install
```

### 2. تنظیمات محیطی

فایل `.env` را ویرایش کنید و API Key خود را از OpenRouter وارد کنید:

```env
OPENROUTER_API_KEY=your-api-key-here
OPENROUTER_MODEL=openai/gpt-4o-mini
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
```

### 3. ساخت دیتابیس

```bash
php artisan migrate
```

### 4. ایجاد Symbolic Link برای Storage

```bash
php artisan storage:link
```

### 5. راه‌اندازی سرور

```bash
php artisan serve
```

سپس به آدرس `http://127.0.0.1:8000` مراجعه کنید.

## نحوه استفاده

1. **انتخاب فایل اول**: فایل Excel یا CSV اول را انتخاب کنید
2. **انتخاب فایل دوم**: فایل Excel یا CSV دوم را انتخاب کنید
3. **توضیحات**: شرح روش مقایسه مورد نظر خود را وارد کنید (اختیاری)
4. **شروع مقایسه**: دکمه "شروع مغایرت‌گیری" را کلیک کنید
5. **مشاهده نتایج**: پس از پردازش، نتایج تحلیل AI نمایش داده می‌شود

## ساختار پروژه

```
├── app/
│   ├── Http/Controllers/
│   │   └── ComparisonController.php    # کنترلر اصلی مقایسه
│   ├── Models/
│   │   └── Comparison.php              # مدل دیتابیس
│   └── Services/
│       └── OpenRouterService.php       # سرویس ارتباط با AI
├── config/
│   └── openrouter.php                  # تنظیمات OpenRouter
├── database/migrations/
│   └── create_comparisons_table.php    # ساختار جدول
├── public/
│   ├── css/
│   │   └── style.css                   # استایل‌های اضافی
│   └── js/
│       ├── script.js                   # منطق اصلی فرانت‌اند
│       └── components/                 # کامپوننت‌های وب
├── resources/views/
│   ├── index.blade.php                 # صفحه اصلی
│   └── result.blade.php                # صفحه نمایش نتایج
└── routes/
    └── web.php                         # مسیرهای وب
```

## تکنولوژی‌های استفاده شده

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS, Feather Icons
- **Excel Processing**: maatwebsite/excel
- **AI Integration**: OpenRouter API
- **Database**: SQLite (قابل تغییر)

## API OpenRouter

برای دریافت API Key:

1. به [OpenRouter.ai](https://openrouter.ai) مراجعه کنید
2. ثبت‌نام کنید و API Key دریافت کنید
3. API Key را در فایل `.env` قرار دهید

## مدل‌های پشتیبانی شده

می‌توانید هر مدلی از OpenRouter را استفاده کنید:

- `openai/gpt-4o-mini` (پیش‌فرض)
- `openai/gpt-4o`
- `anthropic/claude-3-opus`
- `google/gemini-pro`
- و سایر مدل‌های موجود

## پشتیبانی

در صورت بروز مشکل، لطفاً یک Issue ایجاد کنید.

## لایسنس

MIT License
