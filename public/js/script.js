// Main JavaScript functionality for file comparison system

// Initialize feather icons
feather.replace();

let selectedFile1 = null;
let selectedFile2 = null;

// Show selected file names
document.getElementById('file1').addEventListener('change', function(e) {
    selectedFile1 = e.target.files[0];
    document.getElementById('file1-name').textContent = selectedFile1?.name || 'هیچ فایلی انتخاب نشده';
});

document.getElementById('file2').addEventListener('change', function(e) {
    selectedFile2 = e.target.files[0];
    document.getElementById('file2-name').textContent = selectedFile2?.name || 'هیچ فایلی انتخاب نشده';
});

// Handle comparison submission
document.getElementById('start-comparison').addEventListener('click', async function() {
    // Validate files
    if (!selectedFile1 || !selectedFile2) {
        alert('لطفاً هر دو فایل را انتخاب کنید');
        return;
    }

    // Validate file types
    const allowedExtensions = ['xlsx', 'xls', 'csv'];
    const file1Extension = selectedFile1.name.split('.').pop().toLowerCase();
    const file2Extension = selectedFile2.name.split('.').pop().toLowerCase();

    if (!allowedExtensions.includes(file1Extension) || !allowedExtensions.includes(file2Extension)) {
        alert('فقط فایل‌های Excel (xlsx, xls) و CSV پذیرفته می‌شوند');
        return;
    }

    // Get description
    const description = document.getElementById('description').value;

    // Create FormData
    const formData = new FormData();
    formData.append('file1', selectedFile1);
    formData.append('file2', selectedFile2);
    formData.append('description', description);

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Show loading state
    const button = this;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i data-feather="loader" class="ml-2 animate-spin"></i> در حال پردازش...';
    feather.replace();

    try {
        const response = await fetch('/comparison/upload', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            // Redirect to results page
            window.location.href = result.redirect_url;
        } else {
            alert('خطا: ' + result.message);
            button.disabled = false;
            button.innerHTML = originalText;
            feather.replace();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('خطا در ارسال فایل‌ها. لطفاً دوباره تلاش کنید.');
        button.disabled = false;
        button.innerHTML = originalText;
        feather.replace();
    }
});

// Additional functionality can be added here