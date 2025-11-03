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
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        .step::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e5e7eb;
            z-index: -1;
        }
        .step:last-child::after {
            display: none;
        }
        .step.active .step-number {
            background: #10b981;
            color: white;
        }
        .step.completed .step-number {
            background: #10b981;
            color: white;
        }
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .hidden {
            display: none !important;
        }
        .progress-bar {
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="font-vazirmatn bg-gray-50">
    <custom-navbar></custom-navbar>
    
    <main class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">سیستم مغایرت‌گیری هوشمند</h1>
        
        <!-- Step Indicator -->
        <div class="step-indicator mb-8">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <p class="text-sm text-gray-600">بارگذاری فایل‌ها</p>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <p class="text-sm text-gray-600">انتخاب ستون‌های کلیدی</p>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <p class="text-sm text-gray-600">پیش‌نمایش تطبیق</p>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <p class="text-sm text-gray-600">پردازش و نتیجه</p>
            </div>
        </div>

        <!-- Step 1: File Upload -->
        <div id="step-1" class="step-content">
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">بارگذاری فایل‌ها</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- File Uploader 1 -->
                    <div class="border-2 border-dashed border-green-200 rounded-lg p-4 hover:border-green-300 transition">
                        <div class="flex flex-col items-center justify-center py-8">
                            <i data-feather="upload-cloud" class="w-12 h-12 text-green-500 mb-3"></i>
                            <p class="text-gray-600 mb-2">فایل اول را اینجا رها کنید</p>
                            <input type="file" accept=".xlsx,.xls,.csv" class="hidden" id="file1">
                            <button onclick="document.getElementById('file1').click()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                                انتخاب فایل
                            </button>
                        </div>
                        <div id="file1-info" class="text-center text-sm text-gray-500 mt-2"></div>
                    </div>

                    <!-- File Uploader 2 -->
                    <div class="border-2 border-dashed border-green-200 rounded-lg p-4 hover:border-green-300 transition">
                        <div class="flex flex-col items-center justify-center py-8">
                            <i data-feather="upload-cloud" class="w-12 h-12 text-green-500 mb-3"></i>
                            <p class="text-gray-600 mb-2">فایل دوم را اینجا رها کنید</p>
                            <input type="file" accept=".xlsx,.xls,.csv" class="hidden" id="file2">
                            <button onclick="document.getElementById('file2').click()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition">
                                انتخاب فایل
                            </button>
                        </div>
                        <div id="file2-info" class="text-center text-sm text-gray-500 mt-2"></div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button id="analyze-files" disabled class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition flex items-center disabled:bg-gray-400 disabled:cursor-not-allowed">
                        <i data-feather="search" class="ml-2"></i>
                        تحلیل ساختار فایل‌ها
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Column Selection -->
        <div id="step-2" class="step-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">انتخاب ستون‌های کلیدی برای تطبیق</h2>
                
                <div class="mb-6">
                    <div class="bg-blue-50 border-r-4 border-blue-500 p-4 mb-4">
                        <p class="text-sm text-blue-800">
                            <strong>زمان تخمینی پردازش:</strong> <span id="estimated-time">در حال محاسبه...</span>
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- File 1 Columns -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">ستون‌های فایل اول:</h3>
                        <div id="file1-columns" class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <!-- Will be populated dynamically -->
                        </div>
                        <p class="text-xs text-gray-500 mt-2">تعداد ردیف‌ها: <span id="file1-rows">0</span></p>
                    </div>

                    <!-- File 2 Columns -->
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-3">ستون‌های فایل دوم:</h3>
                        <div id="file2-columns" class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                            <!-- Will be populated dynamically -->
                        </div>
                        <p class="text-xs text-gray-500 mt-2">تعداد ردیف‌ها: <span id="file2-rows">0</span></p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">استراتژی تطبیق:</label>
                    <select id="join-strategy" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500">
                        <option value="inner_join">Inner Join - فقط ردیف‌های تطبیق یافته</option>
                        <option value="left_join">Left Join - همه از فایل اول + تطبیق‌ها</option>
                        <option value="full_join">Full Join - همه ردیف‌ها از هر دو فایل</option>
                        <option value="compare_all">مقایسه همه - ردیف به ردیف</option>
                    </select>
                </div>

                <div id="key-columns-section" class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        ستون‌های کلیدی برای تطبیق (مثل "ln" یا "ID"):
                    </label>
                    <div id="common-columns" class="space-y-2">
                        <!-- Will be populated with common columns -->
                    </div>
                    <p class="text-xs text-gray-500 mt-2">ستون‌هایی که در هر دو فایل وجود دارند</p>
                </div>

                <div class="flex justify-between">
                    <button id="back-to-step1" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-md transition">
                        بازگشت
                    </button>
                    <button id="validate-matching" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition flex items-center">
                        <i data-feather="check-circle" class="ml-2"></i>
                        پیش‌نمایش تطبیق
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Preview Matching -->
        <div id="step-3" class="step-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">پیش‌نمایش نتایج تطبیق</h2>
                
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-green-600" id="matched-count">0</p>
                        <p class="text-sm text-gray-600">ردیف تطبیق یافته</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-yellow-600" id="unmatched-file1-count">0</p>
                        <p class="text-sm text-gray-600">ردیف بدون جفت (فایل 1)</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                        <p class="text-2xl font-bold text-orange-600" id="unmatched-file2-count">0</p>
                        <p class="text-sm text-gray-600">ردیف بدون جفت (فایل 2)</p>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="font-semibold text-gray-700 mb-3">نمونه ردیف‌های تطبیق یافته (5 ردیف اول):</h3>
                    <div id="sample-matched" class="overflow-x-auto">
                        <!-- Will be populated dynamically -->
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">توضیحات فرآیند مغایرت‌گیری:</label>
                    <textarea 
                        id="description"
                        class="w-full border border-gray-300 rounded-lg p-4 focus:ring-2 focus:ring-green-500 focus:border-transparent min-h-32"
                        placeholder="شرح روش مغایرت‌گیری را اینجا وارد کنید..."></textarea>
                </div>

                <div class="flex justify-between">
                    <button id="back-to-step2" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-md transition">
                        بازگشت
                    </button>
                    <button id="start-comparison" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-md transition flex items-center">
                        <i data-feather="play" class="ml-2"></i>
                        شروع مقایسه نهایی
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 4: Processing -->
        <div id="step-4" class="step-content hidden">
            <div class="bg-white rounded-xl shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 text-center">در حال پردازش...</h2>
                
                <div class="mb-6">
                    <div class="bg-gray-200 rounded-full h-4 mb-2">
                        <div id="progress-bar" class="progress-bar bg-green-600 h-4 rounded-full"></div>
                    </div>
                    <p id="progress-message" class="text-sm text-gray-600 text-center">در حال آماده‌سازی...</p>
                </div>

                <div class="space-y-2" id="progress-steps">
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="w-6 h-6 rounded-full bg-gray-300 ml-3 flex items-center justify-center">
                            <i data-feather="circle" class="w-4 h-4"></i>
                        </div>
                        <span>تشخیص ساختار فایل‌ها</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="w-6 h-6 rounded-full bg-gray-300 ml-3 flex items-center justify-center">
                            <i data-feather="circle" class="w-4 h-4"></i>
                        </div>
                        <span>تطبیق ردیف‌ها</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="w-6 h-6 rounded-full bg-gray-300 ml-3 flex items-center justify-center">
                            <i data-feather="circle" class="w-4 h-4"></i>
                        </div>
                        <span>تحلیل ساختاری</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="w-6 h-6 rounded-full bg-gray-300 ml-3 flex items-center justify-center">
                            <i data-feather="circle" class="w-4 h-4"></i>
                        </div>
                        <span>تحلیل هوشمند با AI</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/components/navbar.js') }}"></script>
    <script src="{{ asset('js/components/footer.js') }}"></script>
    <script src="{{ asset('js/components/help.js') }}"></script>
    <script src="{{ asset('js/components/contact.js') }}"></script>
    
    <script>
        // Initialize Feather Icons
        feather.replace();

        // State management
        let state = {
            currentStep: 1,
            comparisonId: null,
            file1Schema: null,
            file2Schema: null,
            selectedKeyColumns: [],
            joinStrategy: 'inner_join',
            matchingResult: null
        };

        // Update step indicator
        function updateStepIndicator(step) {
            document.querySelectorAll('.step').forEach((el, index) => {
                const stepNum = index + 1;
                el.classList.remove('active', 'completed');
                if (stepNum < step) {
                    el.classList.add('completed');
                } else if (stepNum === step) {
                    el.classList.add('active');
                }
            });
        }

        // Show specific step
        function showStep(step) {
            document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
            document.getElementById(`step-${step}`).classList.remove('hidden');
            state.currentStep = step;
            updateStepIndicator(step);
            feather.replace();
        }

        // File selection handlers
        function setupFileListeners() {
            const file1 = document.getElementById('file1');
            const file2 = document.getElementById('file2');
            
            if (!file1 || !file2) {
                console.error('File inputs not found');
                return;
            }
            
            file1.addEventListener('change', function(e) {
                const file = e.target.files[0];
                console.log('File 1 selected:', file);
                if (file) {
                    document.getElementById('file1-info').innerHTML = `
                        <span class="text-green-600 font-semibold">${file.name}</span>
                        <br><span class="text-xs">${(file.size / 1024).toFixed(2)} KB</span>
                    `;
                    checkFilesSelected();
                }
            });

            file2.addEventListener('change', function(e) {
                const file = e.target.files[0];
                console.log('File 2 selected:', file);
                if (file) {
                    document.getElementById('file2-info').innerHTML = `
                        <span class="text-green-600 font-semibold">${file.name}</span>
                        <br><span class="text-xs">${(file.size / 1024).toFixed(2)} KB</span>
                    `;
                    checkFilesSelected();
                }
            });
        }

        function checkFilesSelected() {
            const file1 = document.getElementById('file1').files[0];
            const file2 = document.getElementById('file2').files[0];
            const analyzeBtn = document.getElementById('analyze-files');
            console.log('Check files:', {file1, file2, disabled: !(file1 && file2)});
            analyzeBtn.disabled = !(file1 && file2);
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupFileListeners);
        } else {
            setupFileListeners();
        }

        // Step 1: Analyze files
        document.getElementById('analyze-files').addEventListener('click', async function() {
            const file1 = document.getElementById('file1').files[0];
            const file2 = document.getElementById('file2').files[0];
            
            if (!file1 || !file2) {
                alert('لطفاً هر دو فایل را انتخاب کنید');
                return;
            }

            // Show loading
            this.disabled = true;
            this.innerHTML = '<i data-feather="loader" class="ml-2 animate-spin"></i> در حال تحلیل...';
            feather.replace();

            const formData = new FormData();
            formData.append('file1', file1);
            formData.append('file2', file2);

            try {
                const response = await fetch('/preview-columns', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    state.comparisonId = result.comparison_id;
                    state.file1Schema = result.file1;
                    state.file2Schema = result.file2;
                    
                    // Populate columns
                    populateColumns();
                    
                    // Show estimated time
                    document.getElementById('estimated-time').textContent = result.time_estimate.message;
                    
                    // Go to step 2
                    showStep(2);
                } else {
                    alert('خطا: ' + result.message);
                }
            } catch (error) {
                alert('خطا در ارتباط با سرور: ' + error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i data-feather="search" class="ml-2"></i> تحلیل ساختار فایل‌ها';
                feather.replace();
            }
        });

        // Populate columns in step 2
        function populateColumns() {
            const file1Cols = state.file1Schema.columns;
            const file2Cols = state.file2Schema.columns;
            
            // Show file 1 columns
            document.getElementById('file1-columns').innerHTML = file1Cols.map(col => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm">${col}</span>
                    <span class="text-xs text-gray-500">${state.file1Schema.data_types[col] || 'string'}</span>
                </div>
            `).join('');
            
            // Show file 2 columns
            document.getElementById('file2-columns').innerHTML = file2Cols.map(col => `
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm">${col}</span>
                    <span class="text-xs text-gray-500">${state.file2Schema.data_types[col] || 'string'}</span>
                </div>
            `).join('');
            
            // Show row counts
            document.getElementById('file1-rows').textContent = state.file1Schema.row_count;
            document.getElementById('file2-rows').textContent = state.file2Schema.row_count;
            
            // Find common columns
            const commonCols = file1Cols.filter(col => file2Cols.includes(col));
            document.getElementById('common-columns').innerHTML = commonCols.map(col => `
                <label class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer hover:bg-blue-100">
                    <input type="checkbox" value="${col}" class="key-column-checkbox ml-3 w-5 h-5 text-green-600">
                    <span class="font-semibold">${col}</span>
                </label>
            `).join('');
        }

        // Join strategy change
        document.getElementById('join-strategy').addEventListener('change', function() {
            state.joinStrategy = this.value;
            document.getElementById('key-columns-section').style.display = 
                this.value === 'compare_all' ? 'none' : 'block';
        });

        // Step 2: Validate matching
        document.getElementById('validate-matching').addEventListener('click', async function() {
            const keyColumns = Array.from(document.querySelectorAll('.key-column-checkbox:checked'))
                .map(cb => cb.value);
            
            if (state.joinStrategy !== 'compare_all' && keyColumns.length === 0) {
                alert('لطفاً حداقل یک ستون کلیدی انتخاب کنید');
                return;
            }
            
            state.selectedKeyColumns = keyColumns;
            
            // Show loading
            this.disabled = true;
            this.innerHTML = '<i data-feather="loader" class="ml-2 animate-spin"></i> در حال بررسی...';
            feather.replace();

            try {
                const response = await fetch('/validate-matching', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        comparison_id: state.comparisonId,
                        key_columns: keyColumns,
                        join_strategy: state.joinStrategy
                    })
                });

                const result = await response.json();
                
                if (result.success) {
                    state.matchingResult = result;
                    
                    // Update counts
                    document.getElementById('matched-count').textContent = result.matched_count;
                    document.getElementById('unmatched-file1-count').textContent = result.unmatched_file1_count;
                    document.getElementById('unmatched-file2-count').textContent = result.unmatched_file2_count;
                    
                    // Show sample matched rows
                    displaySampleMatched(result.sample_matched);
                    
                    // Go to step 3
                    showStep(3);
                } else {
                    alert('خطا: ' + result.message);
                }
            } catch (error) {
                alert('خطا در ارتباط با سرور: ' + error.message);
            } finally {
                this.disabled = false;
                this.innerHTML = '<i data-feather="check-circle" class="ml-2"></i> پیش‌نمایش تطبیق';
                feather.replace();
            }
        });

        // Display sample matched rows
        function displaySampleMatched(samples) {
            if (!samples || samples.length === 0) {
                document.getElementById('sample-matched').innerHTML = '<p class="text-gray-500 text-center py-4">هیچ ردیف تطبیق یافته‌ای وجود ندارد</p>';
                return;
            }
            
            const file1Cols = Object.keys(samples[0].file1);
            
            const html = `
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">ردیف</th>
                            ${file1Cols.map(col => `<th class="px-3 py-2 text-right text-xs font-semibold text-gray-500 uppercase">${col}</th>`).join('')}
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500 uppercase">مقایسه</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${samples.map((sample, idx) => {
                            const hasChanges = file1Cols.some(col => 
                                JSON.stringify(sample.file1[col]) !== JSON.stringify(sample.file2[col])
                            );
                            
                            return `
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2 text-gray-900 font-semibold">${idx + 1}</td>
                                    ${file1Cols.map(col => {
                                        const val1 = sample.file1[col];
                                        const val2 = sample.file2[col];
                                        const isDifferent = JSON.stringify(val1) !== JSON.stringify(val2);
                                        
                                        return `
                                            <td class="px-3 py-2">
                                                <div class="${isDifferent ? 'text-red-600 font-semibold' : 'text-gray-700'}">
                                                    ${val1 ?? '-'}
                                                </div>
                                                ${isDifferent ? `<div class="text-green-600 text-xs">→ ${val2 ?? '-'}</div>` : ''}
                                            </td>
                                        `;
                                    }).join('')}
                                    <td class="px-3 py-2 text-center">
                                        ${hasChanges ? '<span class="text-red-600">⚠️</span>' : '<span class="text-green-600">✓</span>'}
                                    </td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
            
            document.getElementById('sample-matched').innerHTML = html;
        }

        // Step 3: Start comparison
        document.getElementById('start-comparison').addEventListener('click', async function() {
            const description = document.getElementById('description').value;
            
            // Show step 4 (processing)
            showStep(4);
            
            // Simulate progress
            let progress = 0;
            const progressBar = document.getElementById('progress-bar');
            const progressMessage = document.getElementById('progress-message');
            
            const messages = [
                'در حال تشخیص ساختار فایل‌ها...',
                'در حال تطبیق ردیف‌ها...',
                'در حال تحلیل ساختاری...',
                'در حال تحلیل هوشمند با AI...'
            ];
            
            let messageIndex = 0;
            const progressInterval = setInterval(() => {
                progress += 2;
                if (progress > 95) progress = 95; // Stop at 95% until completed
                progressBar.style.width = progress + '%';
                
                if (progress % 25 === 0 && messageIndex < messages.length) {
                    progressMessage.textContent = messages[messageIndex++];
                }
            }, 500);

            try {
                // Start the job
                const response = await fetch('/start-comparison', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        comparison_id: state.comparisonId,
                        key_columns: state.selectedKeyColumns,
                        join_strategy: state.joinStrategy,
                        description: description
                    })
                });

                const result = await response.json();
                
                if (result.success && result.status === 'processing') {
                    // Start polling for status
                    const pollInterval = setInterval(async () => {
                        try {
                            const statusResponse = await fetch(`/check-status/${state.comparisonId}`);
                            const statusData = await statusResponse.json();
                            
                            if (statusData.success) {
                                if (statusData.status === 'completed') {
                                    clearInterval(pollInterval);
                                    clearInterval(progressInterval);
                                    progress = 100;
                                    progressBar.style.width = '100%';
                                    progressMessage.textContent = 'تکمیل شد! در حال انتقال...';
                                    
                                    setTimeout(() => {
                                        window.location.href = statusData.redirect_url;
                                    }, 500);
                                } else if (statusData.status === 'failed') {
                                    clearInterval(pollInterval);
                                    clearInterval(progressInterval);
                                    alert('خطا در پردازش. لطفاً دوباره تلاش کنید.');
                                    showStep(3);
                                }
                            }
                        } catch (error) {
                            console.error('خطا در بررسی وضعیت:', error);
                        }
                    }, 2000); // Check every 2 seconds
                    
                } else {
                    clearInterval(progressInterval);
                    alert('خطا: ' + result.message);
                    showStep(3);
                }
            } catch (error) {
                clearInterval(progressInterval);
                alert('خطا در ارتباط با سرور: ' + error.message);
                showStep(3);
            }
        });

        // Back buttons
        document.getElementById('back-to-step1').addEventListener('click', () => showStep(1));
        document.getElementById('back-to-step2').addEventListener('click', () => showStep(2));
    </script>
</body>
</html>
