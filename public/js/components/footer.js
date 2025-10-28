// Custom Footer Component

class CustomFooter extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <footer class="bg-gray-800 text-white mt-16">
                <div class="container mx-auto px-4 py-8">
                    <div class="text-center">
                        <p class="text-gray-300">© ${new Date().getFullYear()} مغایرت‌یاب - سیستم مقایسه فایل‌ها</p>
                        <p class="text-gray-400 text-sm mt-2">تمامی حقوق محفوظ است</p>
                    </div>
                </div>
            </footer>
        `;
    }
}

customElements.define('custom-footer', CustomFooter);