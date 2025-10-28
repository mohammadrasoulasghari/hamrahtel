// Custom Navbar Component

class CustomNavbar extends HTMLElement {
    connectedCallback() {
        this.innerHTML = `
            <nav class="bg-white shadow-sm border-b">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <h1 class="text-xl font-bold text-gray-800">مغایرت‌یاب</h1>
                        </div>
                        <div class="flex items-center space-x-4 space-x-reverse">
                            <button class="text-gray-600 hover:text-gray-800 transition">
                                <i data-feather="help-circle"></i>
                            </button>
                            <button class="text-gray-600 hover:text-gray-800 transition">
                                <i data-feather="phone"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        `;
        // Re-initialize feather icons for the navbar
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }
}

customElements.define('custom-navbar', CustomNavbar);