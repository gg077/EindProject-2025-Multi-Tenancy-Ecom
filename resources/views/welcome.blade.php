<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DigiMarket</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .hidden { display: none; }
    </style>
</head>
<body class="bg-white min-h-screen flex flex-col scroll-smooth">

<!-- Navigation -->
<header class="bg-white shadow-sm">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo -->
        <a href="#hero" class="flex items-center space-x-2 font-bold text-yellow-400 text-xl" onclick="incrementClickCount()">
            <img src="/web/img/Digimarket-logo2.png" alt="DigiMarket Logo" class="h-10 w-auto">
            <span>DigiMarket</span>
        </a>

        <!-- Nav links -->
        <nav class="flex space-x-6 text-sm font-medium text-gray-800">
            <a href="#features" class="text-xl font-bold hover:text-gray-600">Features</a>
            <a href="#pricing" class="text-xl font-bold hover:text-gray-600">Pricing</a>
            <a href="#testimonials" class="text-xl font-bold hover:text-gray-600">Testimonials</a>
        </nav>

        <!-- Auth buttons -->
        <div class="space-x-2">
            <a href="{{ route('login') }}"
               class="border border-gray-300 text-gray-800 rounded-md hover:bg-gray-100 px-4 py-2 text-sm font-medium  hover:text-gray-600 transition">
                Log In
            </a>
            <a href="{{ route('tenant.register') }}"
               class=" px-4 py-2 rounded-md text-sm font-medium shadow bg-gray-900 text-white hover:bg-gray-800 transition">
                Sign Up
            </a>
        </div>
    </div>
</header>


<!-- Hero Section -->
<section id="hero" class="relative bg-white py-24 overflow-hidden">
    <!-- Background SVG Decoration -->
    <div class="absolute inset-0 -z-10">
        <svg class="w-full h-full opacity-10" xmlns="http://www.w3.org/2000/svg" fill="none">
            <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M40 0H0V40" stroke="#e5e7eb" stroke-width="1" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#grid)" />
        </svg>
    </div>

    <div class="container mx-auto px-6 flex flex-col-reverse lg:flex-row items-center justify-between">
        <!-- Text Content -->
        <div class="lg:w-1/2 mt-10 lg:mt-0 text-center lg:text-left">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                Launch Your Digital Store — Fast, Free & Limitless
            </h1>
            <p class="text-lg text-gray-600 mb-8">
                Sell digital products, manage teams, and track analytics with zero monthly costs. Everything you need. All in one place.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                <a href="{{ route('tenant.register') }}"
                   class="px-6 py-3 bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition">
                    Create Your Store
                </a>
                <a href="#features"
                   class="border border-gray-300 text-gray-800 px-6 py-3 rounded-md font-semibold hover:bg-gray-100 transition">
                    See Features
                </a>
            </div>
        </div>

        <!-- Image -->
        <div class="lg:w-1/2 text-center">
            <img src="/web/img/hero-dashboard-preview.png" alt="Dashboard Preview" class="w-full max-w-lg mx-auto rounded-xl shadow-md">
        </div>
    </div>
</section>



<!-- Features Section -->
<section id="features" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Built for Digital Entrepreneurs</h2>
            <p class="text-xl text-gray-600 mt-2">Launch, manage, and grow your digital store — completely free.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10">
            <!-- Feature 1 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition text-center">
                <img src="/web/img/iconsetup.png" alt="Easy Store Setup" class="mx-auto h-12 mb-4">
                <h3 class="text-lg font-semibold mb-2">Easy Store Setup</h3>
                <p class="text-gray-600 text-sm">Start selling in minutes with our simple onboarding and intuitive dashboard.</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition text-center">
                <img src="/web/img/iconstripe.png" alt="Secure Stripe Payments" class="mx-auto h-12 mb-4">
                <h3 class="text-lg font-semibold mb-2">Secure Stripe Payments</h3>
                <p class="text-gray-600 text-sm">Accept global payments with fast and secure Stripe integration.</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition text-center">
                <img src="/web/img/delivery.png" alt="Automated Delivery" class="mx-auto h-12 mb-4">
                <h3 class="text-lg font-semibold mb-2">Automated Product Delivery</h3>
                <p class="text-gray-600 text-sm">Your customers receive their files instantly after purchase. No manual work needed.</p>
            </div>

            <!-- Feature 4 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition text-center">
                <img src="/web/img/team.png" alt="Team Access" class="mx-auto h-12 mb-4">
                <h3 class="text-lg font-semibold mb-2">Unlimited Team Access</h3>
                <p class="text-gray-600 text-sm">Invite team members and assign roles with full permission control per user.</p>
            </div>

            <!-- Feature 5 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition text-center">
                <img src="/web/img/analystics.png" alt="Advanced Analytics" class="mx-auto h-12 mb-4">
                <h3 class="text-lg font-semibold mb-2">Analytics & Insights</h3>
                <p class="text-gray-600 text-sm">Track sales, traffic, signups and more with real-time performance dashboards.</p>
            </div>

            <!-- Feature 6 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition text-center">
                <img src="/web/img/free.png" alt="Completely Free" class="mx-auto h-12 mb-4">
                <h3 class="text-lg font-semibold mb-2">Completely Free</h3>
                <p class="text-gray-600 text-sm">No subscriptions, no hidden fees. You keep 100% of your earnings.</p>
            </div>
        </div>
    </div>
</section>



<!-- Pricing Section -->
<section id="pricing" class="py-20 bg-white text-center">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Fair & Transparent Pricing</h2>
        <p class="text-lg text-gray-600 mb-12">No subscriptions. No hidden fees. One powerful plan — completely free.</p>

        <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto text-left">
            <!-- Starter Plan -->
            <div class="border rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <h3 class="text-xl font-bold mb-2">Starter</h3>
                <p class="text-gray-500 line-through text-sm">$9/month</p>
                <p class="text-3xl font-bold text-gray-800 mb-1">Included in Free Plan</p>
                <p class="text-sm text-gray-500 mb-4">Basic tools for individual sellers</p>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li>✔️ Up to 5 digital products</li>
                    <li>✔️ Basic analytics</li>
                    <li>✔️ Email support</li>
                </ul>
                <button disabled class="block w-full text-center px-4 py-2 bg-gray-200 text-gray-500 rounded font-semibold cursor-not-allowed">
                    Included in Free Plan
                </button>
            </div>

            <!-- Forever Free Plan -->
            <div class="border-2 border-gray-900 rounded-lg p-6 shadow-lg relative">
                <div class="absolute -top-4 left-4 bg-gray-900 text-white text-xs px-3 py-1 rounded-full font-semibold">Most Popular</div>
                <h3 class="text-xl font-bold mb-2">All-In-One Access</h3>
                <p class="text-gray-500 line-through text-sm">$29/month</p>
                <p class="text-3xl font-bold text-gray-800 mb-1">100% Free — Forever</p>
                <p class="text-sm text-gray-500 mb-4">Unlimited tools for creators & sellers — no strings attached.</p>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li>✔️ Unlimited digital products</li>
                    <li>✔️ Secure Stripe payments</li>
                    <li>✔️ User Management System</li>
                    <li>✔️ Product delivery automation</li>
                    <li>✔️ Analytics and email delivery</li>
                </ul>
                <a href="{{ route('tenant.register') }}" class="block text-center px-4 py-2 bg-gray-900 text-white rounded font-semibold hover:bg-gray-800 transition">
                    Get Started Free
                </a>
            </div>

            <!-- Enterprise Plan -->
            <div class="border rounded-lg p-6 shadow-sm hover:shadow-md transition">
                <h3 class="text-xl font-bold mb-2">Enterprise</h3>
                <p class="text-gray-500 line-through text-sm">$79/month</p>
                <p class="text-3xl font-bold text-gray-800 mb-1">Enterprise Features — Free for All</p>
                <p class="text-sm text-gray-500 mb-4">Advanced features — now part of the free plan.</p>
                <ul class="text-sm text-gray-700 space-y-2 mb-6">
                    <li>✔️ Custom branding</li>
                    <li>✔️ Full analytics suite</li>
                    <li>✔️ API access</li>
                    <li>✔️ Priority support</li>
                </ul>
                <button disabled class="block w-full text-center px-4 py-2 bg-gray-200 text-gray-500 rounded font-semibold cursor-not-allowed">
                    Included in Free Plan
                </button>
            </div>
        </div>
    </div>
</section>



<!-- Testimonials Section -->
<section id="testimonials" class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Loved by Digital Creators</h2>
            <p class="text-lg text-gray-600 mt-2">See what our users have to say</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <img src="https://i.pravatar.cc/100?img=1" alt="Sarah Johnson" class="w-12 h-12 rounded-full object-cover">
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Sarah Johnson</p>
                        <p class="text-sm text-gray-500">Digital Creator</p>
                    </div>
                </div>
                <p class="italic text-gray-700 text-sm">"This platform completely transformed how I sell my digital products. The user interface is intuitive and the commission rates are the best in the market!"</p>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <img src="https://i.pravatar.cc/100?img=2" alt="Michael Chen" class="w-12 h-12 rounded-full object-cover">
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Michael Chen</p>
                        <p class="text-sm text-gray-500">Course Instructor</p>
                    </div>
                </div>
                <p class="italic text-gray-700 text-sm">"I've tried many platforms, but this one stands out. The analytics tools gave me valuable insights into my customers, helping me optimize my product offerings."</p>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white p-6 rounded-lg shadow hover:shadow-md transition">
                <div class="flex items-center mb-4">
                    <img src="https://i.pravatar.cc/100?img=3" alt="Elena Rodriguez" class="w-12 h-12 rounded-full object-cover">
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900">Elena Rodriguez</p>
                        <p class="text-sm text-gray-500">E-book Author</p>
                    </div>
                </div>
                <p class="italic text-gray-700 text-sm">"Setting up my store was incredibly easy! The platform handles all the technical aspects so I can focus on creating content that my audience loves."</p>
            </div>
        </div>
    </div>
</section>


<!-- Footer -->
<footer class="bg-gray-900 text-white py-10 text-center">
    <p class="text-gray-400 mb-2">© 2025 DigiMarket. All rights reserved.</p>
    <a id="super-admin-button" href=""
       class="hidden text-sm text-red-400 hover:text-red-600 font-semibold">
        Super Admin Login
    </a>
</footer>

<!-- Simple JS to reveal Super super-admin after 5 clicks -->
<script>
    let clickCount = 0;
    function incrementClickCount() {
        clickCount++;
        if (clickCount >= 2) {
            document.getElementById('super-super-admin-button').classList.remove('hidden');
        }
    }
</script>

</body>
</html>
