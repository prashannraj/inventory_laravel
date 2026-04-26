<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Documentation - {{ $siteSettings['company_name'] ?? config('app.name', 'Inventory') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .doc-container {
                max-width: 1200px;
                margin: 0 auto;
            }
            .doc-sidebar {
                position: fixed;
                left: 0;
                top: 64px;
                width: 280px;
                height: calc(100vh - 64px);
                overflow-y: auto;
                border-right: 1px solid #e5e7eb;
                background: white;
                padding: 2rem 1.5rem;
                display: none;
            }
            .doc-content {
                margin-left: 0;
                padding: 2rem;
            }
            @media (min-width: 1024px) {
                .doc-sidebar {
                    display: block;
                }
                .doc-content {
                    margin-left: 280px;
                }
            }
            .toc-link {
                display: block;
                padding: 0.5rem 0;
                color: #4b5563;
                text-decoration: none;
                border-left: 3px solid transparent;
                padding-left: 1rem;
            }
            .toc-link:hover {
                color: #4f46e5;
                border-left-color: #4f46e5;
            }
            .toc-link.active {
                color: #4f46e5;
                font-weight: 600;
                border-left-color: #4f46e5;
            }
            .toc-level-2 {
                padding-left: 1.5rem;
                font-size: 0.9rem;
            }
            .toc-level-3 {
                padding-left: 2.5rem;
                font-size: 0.85rem;
            }
            .doc-section {
                scroll-margin-top: 2rem;
            }
            .back-to-top {
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                background: #4f46e5;
                color: white;
                width: 3rem;
                height: 3rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                z-index: 50;
            }
            .back-to-top:hover {
                background: #4338ca;
            }
            .doc-header {
                background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
                color: white;
                padding: 3rem 2rem;
                margin-bottom: 2rem;
                border-radius: 0.5rem;
            }
            .feature-card {
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 0.5rem;
                padding: 1.5rem;
                margin-bottom: 1.5rem;
                transition: all 0.2s;
            }
            .feature-card:hover {
                border-color: #4f46e5;
                box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1);
            }
            .step-number {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 2rem;
                height: 2rem;
                background: #4f46e5;
                color: white;
                border-radius: 50%;
                font-weight: bold;
                margin-right: 1rem;
            }
            .code-block {
                background: #1f2937;
                color: #e5e7eb;
                padding: 1rem;
                border-radius: 0.5rem;
                font-family: 'Courier New', monospace;
                overflow-x: auto;
                margin: 1rem 0;
            }
            .tip-box {
                background: #dbeafe;
                border-left: 4px solid #3b82f6;
                padding: 1rem;
                margin: 1rem 0;
                border-radius: 0 0.5rem 0.5rem 0;
            }
            .warning-box {
                background: #fef3c7;
                border-left: 4px solid #f59e0b;
                padding: 1rem;
                margin: 1rem 0;
                border-radius: 0 0.5rem 0.5rem 0;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">
        <div class="min-h-screen">
            <livewire:layout.navigation />

            <!-- Sidebar for large screens -->
            <div class="doc-sidebar">
                <h3 class="font-bold text-lg mb-4 text-gray-800">Documentation</h3>
                <div class="space-y-1" id="toc-container">
                    <!-- Table of Contents will be generated by JavaScript -->
                </div>
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h4 class="font-semibold text-sm text-gray-500 uppercase tracking-wider mb-3">Quick Links</h4>
                    <a href="{{ route('dashboard') }}" class="toc-link">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('sales.pos') }}" class="toc-link">
                        <i class="fas fa-cash-register mr-2"></i>POS Interface
                    </a>
                    <a href="{{ route('products.index') }}" class="toc-link">
                        <i class="fas fa-boxes mr-2"></i>Products
                    </a>
                    <a href="{{ route('reports.index') }}" class="toc-link">
                        <i class="fas fa-chart-bar mr-2"></i>Reports
                    </a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="doc-content">
                <div class="doc-container">
                    <!-- Header -->
                    <div class="doc-header">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-bold mb-2">Inventory Management System Documentation</h1>
                                <p class="text-indigo-100">Complete guide to using the inventory system with POS features</p>
                            </div>
                            <div class="hidden md:block">
                                <div class="text-5xl">
                                    <i class="fas fa-book"></i>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="bg-white/20 px-3 py-1 rounded-full text-sm">Version 2.1.0</span>
                            <span class="bg-white/20 px-3 py-1 rounded-full text-sm">Last Updated: April 25, 2026</span>
                            <span class="bg-white/20 px-3 py-1 rounded-full text-sm">For: All Users</span>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="mb-8">
                        <div class="relative">
                            <input type="text" id="doc-search" placeholder="Search documentation..." 
                                   class="w-full p-4 pl-12 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                            <div class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Documentation Content -->
                    <div class="prose prose-lg max-w-none" id="doc-content">
                        @php
                            // Read the markdown file
                            $content = file_get_contents(base_path('GUIDELINE.md'));
                            
                            // Simple markdown to HTML conversion for basic formatting
                            function simpleMarkdownToHtml($text) {
                                // Headers
                                $text = preg_replace('/^# (.*$)/m', '<h1 class="text-3xl font-bold mt-8 mb-4 pb-2 border-b">$1</h1>', $text);
                                $text = preg_replace('/^## (.*$)/m', '<h2 class="text-2xl font-bold mt-6 mb-3">$1</h2>', $text);
                                $text = preg_replace('/^### (.*$)/m', '<h3 class="text-xl font-bold mt-4 mb-2">$1</h3>', $text);
                                
                                // Bold and italic
                                $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
                                $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
                                
                                // Lists
                                $text = preg_replace('/^- (.*$)/m', '<li>$1</li>', $text);
                                $text = preg_replace('/(<li>.*<\/li>\n?)+/s', '<ul class="list-disc pl-6 my-4">$0</ul>', $text);
                                
                                // Code blocks
                                $text = preg_replace('/```(.*?)```/s', '<pre class="bg-gray-100 p-4 rounded my-4 overflow-x-auto"><code>$1</code></pre>', $text);
                                
                                // Inline code
                                $text = preg_replace('/`(.*?)`/', '<code class="bg-gray-100 px-1 rounded">$1</code>', $text);
                                
                                // Links
                                $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" class="text-indigo-600 hover:underline">$1</a>', $text);
                                
                                // Paragraphs
                                $text = preg_replace('/^(?!<[h|u|p|d|li|pre]).*$/m', '<p class="my-3">$0</p>', $text);
                                
                                // Remove empty paragraphs
                                $text = preg_replace('/<p class="my-3"><\/p>/', '', $text);
                                
                                // Horizontal rule
                                $text = preg_replace('/^---$/m', '<hr class="my-8 border-gray-300">', $text);
                                
                                return $text;
                            }
                            
                            echo simpleMarkdownToHtml($content);
                        @endphp
                    </div>

                    <!-- Feedback Section -->
                    <div class="mt-12 p-6 bg-white rounded-lg border border-gray-200">
                        <h3 class="text-xl font-bold mb-4">Was this documentation helpful?</h3>
                        <p class="text-gray-600 mb-4">Help us improve by providing feedback or reporting issues.</p>
                        <div class="flex gap-4">
                            <button class="px-4 py-2 bg-green-100 text-green-800 rounded-lg hover:bg-green-200">
                                <i class="fas fa-thumbs-up mr-2"></i>Yes, helpful
                            </button>
                            <button class="px-4 py-2 bg-red-100 text-red-800 rounded-lg hover:bg-red-200">
                                <i class="fas fa-thumbs-down mr-2"></i>Needs improvement
                            </button>
                            <a href="mailto:support@appantech.com.np" class="px-4 py-2 bg-indigo-100 text-indigo-800 rounded-lg hover:bg-indigo-200">
                                <i class="fas fa-envelope mr-2"></i>Contact Support
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to Top Button -->
            <a href="#" class="back-to-top" id="back-to-top">
                <i class="fas fa-arrow-up"></i>
            </a>
        </div>

        <script>
            // Generate Table of Contents
            document.addEventListener('DOMContentLoaded', function() {
                const content = document.getElementById('doc-content');
                const tocContainer = document.getElementById('toc-container');
                const headings = content.querySelectorAll('h1, h2, h3');
                
                let tocHTML = '';
                
                headings.forEach((heading, index) => {
                    // Skip the main title
                    if (heading.textContent.includes('Inventory Management System')) return;
                    
                    const level = heading.tagName.charAt(1);
                    const text = heading.textContent;
                    const id = `section-${index}`;
                    
                    heading.id = id;
                    
                    let paddingClass = '';
                    if (level === '2') paddingClass = 'toc-level-2';
                    if (level === '3') paddingClass = 'toc-level-3';
                    
                    tocHTML += `
                        <a href="#${id}" class="toc-link ${paddingClass}" data-id="${id}">
                            ${text}
                        </a>
                    `;
                });
                
                tocContainer.innerHTML = tocHTML;
                
                // Smooth scrolling for TOC links
                document.querySelectorAll('.toc-link').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const targetId = this.getAttribute('href').substring(1);
                        const targetElement = document.getElementById(targetId);
                        
                        if (targetElement) {
                            window.scrollTo({
                                top: targetElement.offsetTop - 100,
                                behavior: 'smooth'
                            });
                        }
                    });
                });
                
                // Back to top button
                const backToTop = document.getElementById('back-to-top');
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        backToTop.style.display = 'flex';
                    } else {
                        backToTop.style.display = 'none';
                    }
                });
                
                backToTop.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
                
                // Search functionality
                const searchInput = document.getElementById('doc-search');
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const headings = document.querySelectorAll('#doc-content h1, #doc-content h2, #doc-content h3, #doc-content p');
                    
                    headings.forEach(element => {
                        const text = element.textContent.toLowerCase();
                        if (searchTerm && text.includes(searchTerm)) {
                            element.style.backgroundColor = '#fff3cd';
                            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        } else {
                            element.style.backgroundColor = '';
                        }
                    });
                });
                
                // Highlight active section in TOC
                window.addEventListener('scroll', function() {
                    const scrollPosition = window.scrollY + 150;
                    
                    document.querySelectorAll('.doc-section').forEach(section => {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.clientHeight;
                        const sectionId = section.getAttribute('id');
                        
                        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                            document.querySelectorAll('.toc-link').forEach(link => {
                                link.classList.remove('active');
                                if (link.getAttribute('href') === `#${sectionId}`) {
                                    link.classList.add('active');
                                }
                            });
                        }
                    });
                });
            });
        </script>
        
        @stack('scripts')
    </body>
</html>