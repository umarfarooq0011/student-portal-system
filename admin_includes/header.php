<?php
// Authentication is now handled by authsession.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Student Portal</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Notyf (Modern Premium Alerts) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <!-- Mammoth.js for DOCX preview -->
    <script src="https://cdn.jsdelivr.net/npm/mammoth@1.6.0/mammoth.browser.min.js"></script>

    <!-- SheetJS for Excel preview -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Prevent sidebar flash on page load -->
    <script>
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 1024) {
                document.documentElement.classList.add('sidebar-collapsed-init');
            }
        })();
    </script>

    <style>
        /* Initial collapsed state to prevent flash */
        html.sidebar-collapsed-init .sidebar {
            width: 80px !important;
        }
        html.sidebar-collapsed-init .sidebar .sidebar-text {
            display: none !important;
        }
        html.sidebar-collapsed-init .sidebar .nav-link-item {
            justify-content: center !important;
            padding: 0.75rem !important;
            gap: 0 !important;
        }
        html.sidebar-collapsed-init .sidebar .sidebar-nav {
            padding: 1rem 0.5rem !important;
        }
        html.sidebar-collapsed-init .main-wrapper {
            margin-left: 80px !important;
        }
        html.sidebar-collapsed-init .sidebar-toggle {
            left: 66px !important;
        }
        html.sidebar-collapsed-init .sidebar-toggle i {
            transform: rotate(180deg);
        }
        html, body {
            height: 100%;
        }
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Sidebar styles */
        .sidebar {
            width: 280px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: width 0.3s ease-in-out, transform 0.3s ease-in-out;
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            position: fixed;
            top: 26px;
            left: 266px;
            z-index: 1050;
            transition: left 0.3s ease-in-out;
        }

        .sidebar.collapsed ~ .sidebar-toggle,
        .sidebar-toggle.collapsed-pos {
            left: 66px;
        }

        /* Collapsed sidebar styles */
        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-text {
            display: none;
        }

        .sidebar.collapsed .sidebar-logo-link {
            justify-content: center;
        }

        .sidebar.collapsed .sidebar-nav {
            padding: 1rem 0.5rem;
        }

        .sidebar.collapsed .nav-link-item {
            justify-content: center;
            padding: 0.75rem;
            gap: 0;
        }

        .sidebar.collapsed .nav-link-item:hover {
            transform: none;
        }

        /* Tooltip for collapsed sidebar */
        .sidebar.collapsed .nav-link-item {
            position: relative;
        }

        .sidebar.collapsed .nav-link-item::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            margin-left: 0.75rem;
            z-index: 1060;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            pointer-events: none;
        }

        .sidebar.collapsed .nav-link-item::before {
            content: '';
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            border: 5px solid transparent;
            border-right-color: #1e293b;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            margin-left: -2px;
            pointer-events: none;
        }

        .sidebar.collapsed .nav-link-item:hover::after,
        .sidebar.collapsed .nav-link-item:hover::before {
            opacity: 1;
            visibility: visible;
        }

        /* Ensure nav items are fully clickable */
        .sidebar.collapsed .nav-link-item {
            cursor: pointer;
        }

        .sidebar.collapsed .nav-link-item > * {
            pointer-events: none;
        }

        .sidebar .sidebar-text {
            transition: opacity 0.2s ease, width 0.2s ease;
        }

        .main-wrapper {
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease-in-out;
        }

        .main-wrapper.sidebar-collapsed {
            margin-left: 80px;
        }

        .nav-link-item {
            transition: all 0.2s ease;
        }

        .nav-link-item:hover {
            transform: translateX(4px);
        }

        .nav-link-item.active {
            background: rgba(14, 165, 233, 0.1);
            border-left: 3px solid #0ea5e9;
        }

        /* Card hover effects */
        .premium-card {
            transition: all 0.3s ease;
        }

        .premium-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Accent text */
        .gradient-text {
            color: #0ea5e9;
        }

        /* Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1030;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1;
        }

        /* Mobile responsive */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .sidebar.collapsed {
                width: 280px !important;
            }
            .sidebar.collapsed .sidebar-text {
                display: block;
            }
            .sidebar.collapsed .nav-link-item {
                justify-content: flex-start;
                padding: 0.75rem 1rem;
                gap: 0.75rem;
            }
            .sidebar.collapsed .nav-link-item:hover {
                transform: translateX(4px);
            }
            .sidebar.collapsed .nav-link-item::after,
            .sidebar.collapsed .nav-link-item::before {
                display: none !important;
            }
            .sidebar.collapsed .nav-link-item > * {
                pointer-events: auto;
            }
            .sidebar.collapsed .sidebar-nav {
                padding: 1rem;
            }
            .main-wrapper {
                margin-left: 0 !important;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .sidebar-toggle {
                display: none !important;
            }
        }

        /* Pulse animation */
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.8; }
        }
        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }

        /* ============ LOADER STYLES ============ */
        /* Page Loader */
        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .page-loader.active {
            opacity: 1;
            visibility: visible;
        }

        .loader-content {
            text-align: center;
        }

        .loader-spinner {
            position: relative;
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
        }

        .spinner-ring {
            position: absolute;
            inset: 0;
            border: 3px solid transparent;
            border-radius: 50%;
        }

        .spinner-ring:nth-child(1) {
            border-top-color: #0ea5e9;
            animation: spin 1s linear infinite;
        }

        .spinner-ring:nth-child(2) {
            inset: 8px;
            border-right-color: #6366f1;
            animation: spin 1.5s linear infinite reverse;
        }

        .spinner-ring:nth-child(3) {
            inset: 16px;
            border-bottom-color: #10b981;
            animation: spin 2s linear infinite;
        }

        .loader-icon {
            position: absolute;
            inset: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0284c7;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
        }

        .loader-icon i {
            color: white;
            font-size: 1.25rem;
        }

        .loader-text {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(0.95); }
        }

        /* Inline Loader (for cards/sections) */
        .inline-loader {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            z-index: 10;
            border-radius: inherit;
        }

        .inline-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #e2e8f0;
            border-top-color: #0ea5e9;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .inline-loader span {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Button Loader */
        .btn-loader {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 0.5rem;
        }

        .skeleton-text {
            height: 1rem;
            margin-bottom: 0.5rem;
        }

        .skeleton-text:last-child {
            width: 70%;
        }

        .skeleton-avatar {
            width: 40px;
            height: 40px;
            border-radius: 0.75rem;
        }

        .skeleton-card {
            height: 120px;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 font-inter">
    <!-- Soft Background Decorations -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-sky-200/50 blur-3xl animate-pulse-slow"></div>
        <div class="absolute top-1/3 -left-24 h-96 w-96 rounded-full bg-indigo-200/50 blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-0 right-1/4 h-80 w-80 rounded-full bg-emerald-100/60 blur-3xl animate-pulse-slow"></div>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
