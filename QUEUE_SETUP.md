# راه‌اندازی Queue برای پردازش فایل‌های بزرگ

## مشکل قبلی
هنگام آپلود فایل‌های بزرگ (بیش از 5000 ردیف)، خطای **Allowed memory size exhausted** رخ می‌داد چون تمام فایل در memory بارگذاری می‌شد.

## راه‌حل
1. **پردازش Chunk-based**: فایل‌ها به صورت تکه‌تکه (500 ردیف) خوانده می‌شوند
2. **Queue System**: پردازش سنگین به صورت async در background انجام می‌شود
3. **Polling**: رابط کاربری هر 2 ثانیه وضعیت را چک می‌کند

## نحوه استفاده

### 1. اجرای Queue Worker

در یک ترمینال جداگانه، دستور زیر را اجرا کنید:

```bash
php artisan queue:work --tries=1 --timeout=600
```

**توضیحات پارامترها:**
- `--tries=1`: اگر job شکست خورد، دوباره تلاش نکن
- `--timeout=600`: حداکثر 10 دقیقه برای هر job

### 2. اجرای Laravel Server

در ترمینال دیگری:

```bash
php artisan serve
```

### 3. استفاده از سیستم

1. فایل‌ها را آپلود کنید (حداکثر 50MB)
2. ستون‌های کلیدی را انتخاب کنید
3. Preview را بررسی کنید
4. دکمه "شروع پردازش" را بزنید
5. صفحه به صورت خودکار وضعیت را چک می‌کند و بعد از اتمام به نتایج می‌رود

## تنظیمات پیشرفته

### استفاده از Redis (سریع‌تر)

اگر Redis دارید:

```bash
# نصب Redis driver
composer require predis/predis

# تغییر .env
QUEUE_CONNECTION=redis

# راه‌اندازی worker
php artisan queue:work redis --tries=1 --timeout=600
```

### اجرای Permanent با Supervisor

برای production، از Supervisor استفاده کنید:

```ini
[program:hamrahtel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/hamrahtel/artisan queue:work --sleep=3 --tries=1 --timeout=600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/hamrahtel/storage/logs/worker.log
stopwaitsecs=3600
```

## نکات مهم

### حافظه
- تنظیمات PHP:
  - `memory_limit = 512M`
  - `upload_max_filesize = 50M`
  - `post_max_size = 50M`
  - `max_execution_time = 300`

این تنظیمات در `ComparisonController` به صورت خودکار با `ini_set()` اعمال می‌شوند.

### لاگ‌ها
اگر مشکلی پیش آمد، لاگ‌ها را بررسی کنید:

```bash
tail -f storage/logs/laravel.log
```

### پاک‌سازی Queue

اگر job‌های شکست خورده جمع شدند:

```bash
# پاک کردن همه job‌های failed
php artisan queue:flush

# Restart همه worker‌ها
php artisan queue:restart
```

## معماری جدید

### فلو پردازش:

1. **Preview (سریع)**: فقط 10 ردیف اول خوانده می‌شود
2. **Validate (سریع)**: تعداد match‌ها حساب می‌شود
3. **Start (async)**: Job به queue می‌رود
4. **Poll (هر 2 ثانیه)**: وضعیت چک می‌شود
5. **Result**: بعد از completion نمایش داده می‌شود

### جداول دیتابیس:

- `comparisons.status`: pending | processing | completed | failed
- `jobs`: لیست job‌های در صف
- `failed_jobs`: job‌های شکست خورده

## عیب‌یابی

### خطا: "No such file or directory"
- مطمئن شوید `storage/app/public` وجود دارد
- دستور `php artisan storage:link` را اجرا کنید

### خطا: "Queue connection database not found"
- مطمئن شوید migration `jobs` اجرا شده است
- دستور `php artisan migrate` را اجرا کنید

### Job هیچ وقت تمام نمیشه
- لاگ Laravel را چک کنید
- مطمئن شوید worker در حال اجراست (`ps aux | grep queue:work`)
- timeout را افزایش دهید

---

**نکته**: اگر برای تست سریع می‌خواهید queue استفاده نکنید، می‌توانید در `.env` تنظیم کنید:

```env
QUEUE_CONNECTION=sync
```

اما برای فایل‌های بزرگ، حتماً از `database` یا `redis` استفاده کنید.
