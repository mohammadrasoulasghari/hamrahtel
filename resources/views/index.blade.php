<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مغایرت‌یاب | سیستم مقایسه فایل‌ها</title>
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
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">سیستم مغایرت‌گیری</h1>
        
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">بارگذاری فایل‌ها</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- File Uploader 1 -->
                <div class="border-2 border-dashed border-green-200 rounded-lg p-4 hover:border-green-300 transition">
                    <div class="flex flex-col items-center justify-center py-8">
                        <i data-feather="upload-cloud" class="w-12 h-12 text-green-500 mb-3"></i>
                        <p class="text-gray-600 mb-2">فایل اول را اینجا رها کنید</p>
                        <input type="file" class="hidden" id="file1">
                        <button onclick="document.getElementById('file1').click()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                            انتخاب فایل
                        </button>
                    </div>
                    <div id="file1-name" class="text-center text-sm text-gray-500 mt-2"></div>
                </div>

                <!-- File Uploader 2 -->
                <div class="border-2 border-dashed border-green-200 rounded-lg p-4 hover:border-green-300 transition">
                    <div class="flex flex-col items-center justify-center py-8">
                        <i data-feather="upload-cloud" class="w-12 h-12 text-green-500 mb-3"></i>
                        <p class="text-gray-600 mb-2">فایل دوم را اینجا رها کنید</p>
                        <input type="file" class="hidden" id="file2">
                        <button onclick="document.getElementById('file2').click()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                            انتخاب فایل
                        </button>
                    </div>
                    <div id="file2-name" class="text-center text-sm text-gray-500 mt-2"></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">توضیحات فرآیند مغایرت‌گیری</h2>
            <textarea 
                id="description"
                class="w-full border border-gray-300 rounded-lg p-4 focus:ring-2 focus:ring-green-500 focus:border-transparent min-h-40"
                placeholder="شرح روش مغایرت‌گیری را اینجا وارد کنید..."></textarea>
            
            <div class="mt-6 flex justify-end">
                <button id="start-comparison" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition flex items-center">
                    <i data-feather="play" class="ml-2"></i>
                    شروع مغایرت‌گیری
                </button>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/components/navbar.js') }}"></script>
    <script src="{{ asset('js/components/footer.js') }}"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/components/help.js') }}"></script>
    <script src="{{ asset('js/components/contact.js') }}"></script>
</body>
</html>
