<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتایج مقایسه | سیستم مقایسه فایل‌ها</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100..900&display=swap');
    </style>
</head>
<body class="font-vazirmatn bg-gray-50">
    <custom-navbar></custom-navbar>
    
    <main class="container mx-auto px-4 py-8">
        <!-- عنوان صفحه -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2 text-center">نتایج مقایسه فایل‌ها</h1>
            <p class="text-gray-600 text-center">تحلیل هوش مصنوعی از تفاوت‌های دو فایل</p>
        </div>

        <!-- اطلاعات فایل‌ها -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <i data-feather="file-text" class="ml-2"></i>
                اطلاعات فایل‌ها
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                    <h3 class="font-semibold text-green-800 mb-2 flex items-center">
                        <i data-feather="file" class="ml-2 w-4 h-4"></i>
                        فایل اول
                    </h3>
                    <p class="text-gray-700">{{ $comparison->file1_original_name }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        آپلود شده در: {{ $comparison->created_at->format('Y/m/d H:i') }}
                    </p>
                </div>

                <div class="border border-blue-200 rounded-lg p-4 bg-blue-50">
                    <h3 class="font-semibold text-blue-800 mb-2 flex items-center">
                        <i data-feather="file" class="ml-2 w-4 h-4"></i>
                        فایل دوم
                    </h3>
                    <p class="text-gray-700">{{ $comparison->file2_original_name }}</p>
                    <p class="text-sm text-gray-500 mt-2">
                        آپلود شده در: {{ $comparison->created_at->format('Y/m/d H:i') }}
                    </p>
                </div>
            </div>

            @if($comparison->description)
            <div class="mt-6 border-t pt-4">
                <h3 class="font-semibold text-gray-700 mb-2">توضیحات مقایسه:</h3>
                <p class="text-gray-600 bg-gray-50 p-4 rounded-lg">{{ $comparison->description }}</p>
            </div>
            @endif
        </div>

        <!-- وضعیت پردازش -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i data-feather="activity" class="ml-2 text-green-600"></i>
                    <h2 class="text-xl font-semibold text-gray-700">وضعیت پردازش</h2>
                </div>
                <div>
                    @if($comparison->status == 'completed')
                        <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold">
                            <i data-feather="check-circle" class="inline w-4 h-4"></i>
                            تکمیل شده
                        </span>
                    @elseif($comparison->status == 'processing')
                        <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full text-sm font-semibold">
                            <i data-feather="clock" class="inline w-4 h-4"></i>
                            در حال پردازش
                        </span>
                    @elseif($comparison->status == 'failed')
                        <span class="bg-red-100 text-red-800 px-4 py-2 rounded-full text-sm font-semibold">
                            <i data-feather="x-circle" class="inline w-4 h-4"></i>
                            ناموفق
                        </span>
                    @else
                        <span class="bg-gray-100 text-gray-800 px-4 py-2 rounded-full text-sm font-semibold">
                            <i data-feather="minus-circle" class="inline w-4 h-4"></i>
                            در انتظار
                        </span>
                    @endif
                </div>
            </div>
            
            @if($comparison->processing_started_at && $comparison->processing_completed_at)
            <div class="mt-4 text-sm text-gray-600">
                <p>زمان شروع: {{ $comparison->processing_started_at->format('Y/m/d H:i:s') }}</p>
                <p>زمان پایان: {{ $comparison->processing_completed_at->format('Y/m/d H:i:s') }}</p>
                <p>مدت زمان پردازش: {{ $comparison->processing_started_at->diffInSeconds($comparison->processing_completed_at) }} ثانیه</p>
            </div>
            @endif
        </div>

        <!-- آمار تطبیق -->
        @if($comparison->status == 'completed')
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <i data-feather="bar-chart-2" class="ml-2 text-blue-600"></i>
                آمار تطبیق ردیف‌ها
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-green-50 border-r-4 border-green-500 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">ردیف‌های تطبیق یافته</p>
                            <p class="text-3xl font-bold text-green-600">{{ $comparison->matched_count ?? 0 }}</p>
                        </div>
                        <i data-feather="check-circle" class="w-12 h-12 text-green-500"></i>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border-r-4 border-yellow-500 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">بدون جفت (فایل اول)</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $comparison->unmatched_file1_count ?? 0 }}</p>
                        </div>
                        <i data-feather="alert-circle" class="w-12 h-12 text-yellow-500"></i>
                    </div>
                </div>
                
                <div class="bg-orange-50 border-r-4 border-orange-500 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">بدون جفت (فایل دوم)</p>
                            <p class="text-3xl font-bold text-orange-600">{{ $comparison->unmatched_file2_count ?? 0 }}</p>
                        </div>
                        <i data-feather="alert-circle" class="w-12 h-12 text-orange-500"></i>
                    </div>
                </div>
            </div>
            
            @if($comparison->row_join_strategy)
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-gray-700">
                    <strong>استراتژی تطبیق:</strong> 
                    @switch($comparison->row_join_strategy)
                        @case('inner_join')
                            Inner Join - فقط ردیف‌های تطبیق یافته
                            @break
                        @case('left_join')
                            Left Join - همه از فایل اول + تطبیق‌ها
                            @break
                        @case('full_join')
                            Full Join - همه ردیف‌ها از هر دو فایل
                            @break
                        @case('compare_all')
                            مقایسه همه - ردیف به ردیف
                            @break
                        @default
                            {{ $comparison->row_join_strategy }}
                    @endswitch
                </p>
                @if($comparison->selected_key_columns)
                <p class="text-sm text-gray-700 mt-2">
                    <strong>ستون‌های کلیدی:</strong> 
                    {{ is_array($comparison->selected_key_columns) ? implode(', ', $comparison->selected_key_columns) : $comparison->selected_key_columns }}
                </p>
                @endif
            </div>
            @endif
        </div>

        <!-- نتایج تحلیل ساختاری PHP -->
        @if($comparison->php_analysis_result)
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <i data-feather="code" class="ml-2 text-indigo-600"></i>
                تحلیل ساختاری (PHP)
            </h2>
            
            @php
                $phpAnalysis = is_string($comparison->php_analysis_result) 
                    ? json_decode($comparison->php_analysis_result, true) 
                    : $comparison->php_analysis_result;
            @endphp
            
            @if(isset($phpAnalysis['findings']) && count($phpAnalysis['findings']) > 0)
            <div class="space-y-4">
                @foreach($phpAnalysis['findings'] as $finding)
                <div class="border-r-4 rounded-lg p-4
                    @if($finding['severity'] == 'high') bg-red-50 border-red-500
                    @elseif($finding['severity'] == 'warning') bg-yellow-50 border-yellow-500
                    @else bg-blue-50 border-blue-500
                    @endif">
                    <div class="flex items-start">
                        <div class="ml-3">
                            @if($finding['severity'] == 'high')
                                <i data-feather="alert-octagon" class="w-5 h-5 text-red-600"></i>
                            @elseif($finding['severity'] == 'warning')
                                <i data-feather="alert-triangle" class="w-5 h-5 text-yellow-600"></i>
                            @else
                                <i data-feather="info" class="w-5 h-5 text-blue-600"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800">{{ $finding['message'] }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $finding['category'] }}</span>
                            </p>
                            @if(isset($finding['details']) && is_array($finding['details']))
                            <div class="mt-2 text-sm text-gray-700">
                                @if(isset($finding['details'][0]) && is_string($finding['details'][0]))
                                    <ul class="list-disc mr-5">
                                        @foreach($finding['details'] as $detail)
                                            <li>{{ $detail }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <pre class="bg-gray-100 p-2 rounded text-xs overflow-x-auto">{{ json_encode($finding['details'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">هیچ یافته ساختاری قابل توجهی وجود ندارد</p>
            @endif
        </div>
        @endif
        @endif

        <!-- نتایج تحلیل AI -->
        @if($comparison->status == 'completed' && $comparison->ai_result)
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <i data-feather="cpu" class="ml-2 text-purple-600"></i>
                تحلیل هوش مصنوعی
            </h2>
            
            <div class="prose prose-sm max-w-none">
                <div class="ai-result-content bg-gray-50 p-6 rounded-lg">
                    {!! $comparison->ai_result !!}
                </div>
            </div>
        </div>
        @endif

        <!-- دکمه‌های عملیاتی -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="/" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition flex items-center">
                    <i data-feather="plus" class="ml-2"></i>
                    مقایسه جدید
                </a>
                
                <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-md transition flex items-center">
                    <i data-feather="printer" class="ml-2"></i>
                    چاپ نتایج
                </button>
                
                <button onclick="downloadResults()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-md transition flex items-center">
                    <i data-feather="download" class="ml-2"></i>
                    دانلود PDF
                </button>
            </div>
        </div>
    </main>

    
    <script src="{{ asset('js/components/navbar.js') }}"></script>
    <script src="{{ asset('js/components/footer.js') }}"></script>
    <script src="{{ asset('js/components/help.js') }}"></script>
    <script src="{{ asset('js/components/contact.js') }}"></script>
    <script>
        feather.replace();
        
        function downloadResults() {
            alert('قابلیت دانلود PDF به زودی اضافه می‌شود');
        }

        // Auto refresh for processing status
        @if($comparison->status == 'processing')
        setTimeout(function() {
            window.location.reload();
        }, 5000);
        @endif
    </script>
</body>
</html>
