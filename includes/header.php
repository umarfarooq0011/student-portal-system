<?php
// Authentication is now handled by authsession.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            background-color: #1e1e2d;
            min-height: 100vh;
            color: #fff;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            transition: left 0.3s;
            z-index: 1040;
        }
        
        
        .sidebar .nav-link {
            color: #a2a3b7;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-link i {
            font-size: 1.25rem;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #a259ff, #8438ff);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            height: 100%;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .quick-links {
            background: linear-gradient(135deg, #a259ff, #8438ff);
            color: white;
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .submit-btn {
            background: white;
            color: #8438ff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .submit-btn:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        
        
        @media (max-width: 991.98px) {
            .sidebar {
                left: -250px;
                width: 250px;
                position: fixed;
                height: 100vh;
                z-index: 1040;
            }
            .sidebar.active {
                left: 0;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            .navbar {
                margin-left: 0 !important;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 200px;
            }
        }

        @media (max-width: 380px) {
            .sidebar {
                width: 100vw;
            }
        }
    </style>
</head>
<body>
