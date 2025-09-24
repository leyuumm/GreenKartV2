<?php
include 'config.php';
session_start();

// Handle Sign Up
if (isset($_POST['signup'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $signupError = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $signupError = 'Password must be at least 6 characters.';
    } else {
        $check = $conn->prepare('SELECT * FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $signupError = 'Email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('ssss', $name, $email, $role, $hash);
            if ($stmt->execute()) {
                $signupSuccess = 'Account created successfully!';
            } else {
                $signupError = 'Error creating account.';
            }
        }
    }
}

// Handle Sign In
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            if ($user['role'] === 'seller') {
                header('Location: seller.php');
                exit();
            }
        } else {
            $loginError = 'Invalid password.';
        }
    } else {
        $loginError = 'No account found with that email.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenKart - Find Your Perfect Plant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        .font-montserrat { 
            font-family: 'Montserrat', sans-serif; 
        }
        .font-opensans { 
            font-family: 'Open Sans', sans-serif; 
        }
        
        body {
            font-family: 'Open Sans', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6, .fw-bold {
            font-family: 'Montserrat', sans-serif;
        }
        
        :root {
            --emerald-50: #ecfdf5;
            --emerald-100: #d1fae5;
            --emerald-200: #a7f3d0;
            --emerald-300: #6ee7b7;
            --emerald-400: #34d399;
            --emerald-500: #10b981;
            --emerald-600: #059669;
            --emerald-700: #047857;
        }
        
        .bg-emerald-gradient {
            background: linear-gradient(135deg, var(--emerald-100), var(--emerald-200));
        }
        
        .text-emerald-600 {
            color: var(--emerald-600) !important;
        }
        
        .bg-emerald-600 {
            background-color: var(--emerald-600) !important;
        }
        
        .bg-emerald-700:hover {
            background-color: var(--emerald-700) !important;
        }
        
        .btn-emerald {
            background-color: var(--emerald-600);
            border-color: var(--emerald-600);
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-emerald::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-emerald:hover::before {
            left: 100%;
        }
        
        .btn-emerald:hover {
            background-color: var(--emerald-700);
            border-color: var(--emerald-700);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        
        .btn-danger {
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        
        .plant-card, .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }
        
        .plant-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }
        
        .plant-card:hover::before {
            opacity: 1;
        }
        
        .plant-card:hover, .product-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .plant-card img, .product-card img {
            transition: transform 0.3s ease;
        }
        
        .plant-card:hover img {
            transform: scale(1.1);
        }
        
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-container {
            background: white;
            border-radius: 16px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            transform: scale(0.9) translateY(20px);
            transition: all 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .modal-container-wide {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            max-width: 550px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .modal-overlay.show .modal-container {
            transform: scale(1) translateY(0);
        }

        .modal-icon {
            width: 48px;
            height: 48px;
            background-color: var(--emerald-100);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        
        .modal-icon i {
            font-size: 1.25rem;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
            font-family: 'Montserrat', sans-serif;
        }
        
        .modal-subtitle {
            color: #6b7280;
            margin-bottom: 0;
        }
        
        .modal-form {
            margin-top: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #374151;
        }
        
        .form-input, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--emerald-600);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-label {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            font-size: 0.875rem;
        }
        
        .form-checkbox {
            margin-right: 0.5rem;
            margin-top: 0.125rem;
            accent-color: var(--emerald-600);
        }
        
        .checkbox-text {
            color: #6b7280;
            line-height: 1.4;
        }
        
        .forgot-link, .terms-link {
            color: var(--emerald-600);
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .forgot-link:hover, .terms-link:hover {
            color: var(--emerald-700);
            text-decoration: underline;
        }
        
        .btn-primary {
            width: 100%;
            background-color: var(--emerald-600);
            color: white;
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--emerald-700);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }
        
        .modal-footer {
            margin-top: 1.5rem;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .switch-text {
            color: #6b7280;
            margin: 0;
            text-align: center;
            display: block;
            width: 100%;
        }
        
        .switch-link {
            color: var(--emerald-600);
            background: none;
            border: none;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
        }
        
        .switch-link:hover {
            color: var(--emerald-700);
            text-decoration: underline;
        }
        
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            color: #9ca3af;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: color 0.2s;
        }
        
        .modal-close:hover {
            color: #6b7280;
        }
        
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 6px;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .loading-container {
            text-align: center;
            color: #08946c;
        }
        
        .loading-spinner {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
        }
        
        .spinner-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 3px solid transparent;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }
        
        .spinner-ring:nth-child(1) {
            border-top-color: var(--emerald-400);
            animation-duration: 2s;
        }
        
        .spinner-ring:nth-child(2) {
            border-right-color: var(--emerald-500);
            animation-duration: 1.5s;
            animation-direction: reverse;
        }
        
        .spinner-ring:nth-child(3) {
            border-bottom-color: var(--emerald-600);
            animation-duration: 1s;
        }
        
        .loading-plant {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            color: var(--emerald-500);
            animation: pulse 2s ease-in-out infinite;
        }
        
        .loading-text {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        
        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        .settings-icon {
            color: var(--emerald-600) !important;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .settings-icon:hover {
            color: var(--emerald-700) !important;
            transform: rotate(90deg) scale(1.1);
            filter: drop-shadow(0 0 8px rgba(5, 150, 105, 0.4));
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            border-radius: 8px;
            animation: dropdownSlide 0.2s ease;
            overflow: hidden;
        }
        
        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .dropdown-item {
            transition: all 0.2s ease;
            padding: 0.75rem 1rem;
        }
        
        .dropdown-item:hover {
            background-color: var(--emerald-50);
            color: var(--emerald-700);
            transform: translateX(4px);
        }
        
        .dropdown-item.text-danger:hover {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .hero-icon {
            width: 64px;
            height: 64px;
            background-color: var(--emerald-600);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .search-container {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .search-container:focus-within {
            transform: scale(1.02);
        }
        
        .search-container input:focus {
            box-shadow: 0 0 20px rgba(5, 150, 105, 0.2);
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: translate(-50%, -50%) scale(1); }
            50% { transform: translate(-50%, -50%) scale(1.1); }
        }
        
        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fas fa-leaf text-emerald-600 fs-2 me-2"></i>
                <span class="fw-bold text-emerald-600" id="navTitle">GreenKart</span>
            </a>
            <div class="d-flex align-items-center" id="navButtons">
                <button class="btn text-decoration-none me-3" style="color: #4b5563;" onmouseover="this.style.color='#059669'" onmouseout="this.style.color='#4b5563'" onclick="showLoginModal()">Sign In</button>
                <button class="btn btn-emerald" onclick="showSignupModal()">Sign Up</button>
            </div>
            <div class="d-none align-items-center" id="dashboardNav">
                <span class="text-muted me-3">Welcome, <span id="userName"></span>!</span>
                <div class="dropdown">
                    <button class="btn btn-link settings-icon" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Account Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                        <li id="roleSwitcherItem" style="display: none;">
                            <a class="dropdown-item" href="seller.php">
                                <i class="fas fa-store me-2"></i>
                                <span>Go to Seller Dashboard</span>
                            </a>
                        </li>
                        <li id="roleSwitcherDivider" style="display: none;"><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteAccountConfirm()"><i class="fas fa-trash me-2"></i>Delete Account</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-emerald-gradient py-5">
        <div class="container text-center">
            <div class="hero-icon">
                <i class="fas fa-seedling text-white fs-2"></i>
            </div>
            <h1 class="display-4 fw-bold text-dark mb-3">Discover Amazing Plants</h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                Connect with local plant sellers and find the perfect plants for your home and garden
            </p>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-lg">
                        <div class="card-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8 search-container">
                                    <input type="text" class="form-control form-control-lg" 
                                           placeholder="Search plants by name..." id="searchInput">
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select form-select-lg" id="categoryFilter">
                                        <option value="">All Categories</option>
                                        <option value="indoor">Indoor Plants</option>
                                        <option value="outdoor">Outdoor Plants</option>
                                        <option value="succulents">Succulents</option>
                                        <option value="flowers">Flowers</option>
                                        <option value="herbs">Herbs</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Featured Plants</h2>
            <div id="noResults" class="text-center py-5 d-none">
                <span class="text-muted display-1 d-block mb-3">🔍</span>
                <p class="text-muted fs-5">No plants found matching your search criteria.</p>
            </div>
            <div class="row g-4" id="productsGrid">
                <!-- Products will be populated by JavaScript -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background-color: #111827;" class="text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-leaf text-success fs-4 me-2"></i>
                        <h5 class="mb-0 fw-bold">GreenKart</h5>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
                        <a href="#" class="text-decoration-none me-4" style="color: #9ca3af;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#9ca3af'">About</a>
                        <a href="#" class="text-decoration-none me-4" style="color: #9ca3af;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#9ca3af'">Help</a>
                        <a href="#" class="text-decoration-none me-4" style="color: #9ca3af;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#9ca3af'">Support</a>
                        <a href="#" class="text-decoration-none" style="color: #9ca3af;" onmouseover="this.style.color='#10b981'" onmouseout="this.style.color='#9ca3af'">Terms</a>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="text-center">
                <p class="mb-0" style="color: #9ca3af;">&copy; 2025 GreenKart. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div id="loginModal" class="modal-overlay">
        <div class="modal-container">
            <div class="text-center mb-4">
                <div class="modal-icon">
                    <i class="fas fa-leaf text-emerald-600"></i>
                </div>
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Sign in to your GreenKart account</p>
            </div>
            
            <form id="loginForm" class="modal-form">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter your password" required>
                </div>
                <div class="form-row">
                    <label class="checkbox-label">
                        <input type="checkbox" class="form-checkbox">
                        <span class="checkbox-text">Remember me</span>
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>
                <div id="loginError" class="error-message d-none"></div>
                <button type="submit" class="btn-primary">Sign In</button>
            </form>
            
            <div class="modal-footer">
                <p class="switch-text">Don't have an account? 
                    <button type="button" onclick="switchToSignup()" class="switch-link">Sign up</button>
                </p>
            </div>
            
            <button type="button" onclick="closeModals()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signupModal" class="modal-overlay">
        <div class="modal-container-wide">
            <div class="text-center mb-4">
                <div class="modal-icon">
                    <i class="fas fa-seedling text-emerald-600"></i>
                </div>
                <h2 class="modal-title">Join GreenKart</h2>
                <p class="modal-subtitle">Create your account to start your plant journey</p>
            </div>
            
            <form id="signupForm" class="modal-form">
                <form method="post" class="modal-form">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-input" placeholder="Enter your full name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="Enter your email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-input" required>
                            <option value="">Select your role</option>
                            <option value="buyer">Buyer</option>
                            <option value="seller">Seller</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Create a password" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirmPassword" class="form-input" placeholder="Confirm your password" required>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" class="form-checkbox" required>
                            <span class="checkbox-text">I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a></span>
                        </label>
                    </div>
                    <?php if (!empty($signupError)): ?>
                        <div class="error-message"><?= $signupError ?></div>
                    <?php elseif (!empty($signupSuccess)): ?>
                        <div class="text-success"><?= $signupSuccess ?></div>
                    <?php endif; ?>
                    <button type="submit" name="signup" class="btn-primary">Create Account</button>
                </form>
            
            <div class="modal-footer">
                <p class="switch-text">Already have an account? 
                    <button type="button" onclick="switchToLogin()" class="switch-link">Sign in</button>
                </p>
            </div>
            
            <button type="button" onclick="closeModals()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="productModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <img id="productModalImage" class="img-fluid rounded mb-3" style="height: 300px; width: 100%; object-fit: cover;">
                            <p id="productModalDescription" class="text-muted mb-3"></p>
                            <div class="bg-light p-3 rounded">
                                <p class="fs-3 fw-bold text-emerald-600 mb-2" id="productModalPrice"></p>
                                <p class="text-muted mb-0">Category: <span id="productModalCategory"></span></p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h5 class="fw-bold mb-3">Seller Information</h5>
                            <div class="seller-info mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-user text-emerald-600 me-3"></i>
                                    <span class="fw-medium" id="sellerName"></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone text-emerald-600 me-3"></i>
                                    <span id="sellerPhone"></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-star text-warning me-3"></i>
                                    <span id="sellerRating"></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-map-marker-alt text-emerald-600 me-3"></i>
                                    <span id="sellerLocation"></span>
                                </div>
                            </div>
                            
                            <div class="map-placeholder mb-3">
                                <i class="fas fa-map text-muted fs-1 mb-2"></i>
                                <p class="text-muted mb-1">Interactive Map</p>
                                <small class="text-muted" id="sellerCoordinates"></small>
                            </div>
                            
                            <button class="btn btn-emerald w-100">Contact Seller</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Screen -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-container">
            <div class="loading-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="loading-plant">
                    <i class="fas fa-seedling"></i>
                </div>
            </div>
            <div class="loading-text" id="loadingText">Loading<span class="loading-dots"></span></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentUser = null;
        let filteredProducts = [];

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            // Check for existing user session
            const savedUser = localStorage.getItem('currentUser');
            if (savedUser) {
                currentUser = JSON.parse(savedUser);
                showDashboard();
            }
            
            setupEventListeners();
            renderProducts();
        });

        function setupEventListeners() {
            // Search and filter
            document.getElementById('searchInput').addEventListener('input', filterProducts);
            document.getElementById('categoryFilter').addEventListener('change', filterProducts);
            
            // Forms
            document.getElementById('loginForm').addEventListener('submit', handleLogin);
            document.getElementById('signupForm').addEventListener('submit', handleSignup);
        }

        function filterProducts() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            
            const allSellerProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
            filteredProducts = allSellerProducts.filter(product => {
                const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                    product.description.toLowerCase().includes(searchTerm);
                const matchesCategory = !categoryFilter || product.category === categoryFilter;
                return matchesSearch && matchesCategory;
            });
            
            renderProducts();
        }

        function renderProducts() {
            const grid = document.getElementById('productsGrid');
            const noResults = document.getElementById('noResults');
            
            const allSellerProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
            filteredProducts = allSellerProducts.filter(product => {
                const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
                const selectedCategory = document.getElementById('categoryFilter')?.value || '';
                
                const matchesSearch = product.name.toLowerCase().includes(searchTerm) || 
                                    product.description.toLowerCase().includes(searchTerm);
                const matchesCategory = selectedCategory === '' || product.category === selectedCategory;
                
                return matchesSearch && matchesCategory;
            });
            
            if (filteredProducts.length === 0) {
                grid.innerHTML = '';
                noResults.classList.remove('d-none');
                return;
            }
            
            noResults.classList.add('d-none');
            
            grid.innerHTML = filteredProducts.map(product => `
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card plant-card h-100" onclick="openProductModal(${product.id})">
                        <img src="${product.image}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="text-emerald-600 fw-bold fs-5 mb-2">₱${product.price}</p>
                            <span class="badge bg-secondary">${product.category}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function openProductModal(productId) {
            if (!currentUser) {
                showLoginModal();
                return;
            }
            
            const allSellerProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
            const product = allSellerProducts.find(p => p.id === productId);
            if (!product) return;
            
            document.getElementById('productModalTitle').textContent = product.name;
            document.getElementById('productModalImage').src = product.image;
            document.getElementById('productModalDescription').textContent = product.description;
            document.getElementById('productModalPrice').textContent = `₱${product.price}`;
            document.getElementById('productModalCategory').textContent = product.category;
            
            document.getElementById('sellerName').textContent = product.seller?.name || 'Unknown Seller';
            document.getElementById('sellerPhone').textContent = product.seller?.phone || 'Not provided';
            document.getElementById('sellerRating').textContent = product.seller?.rating || 'New Seller';
            document.getElementById('sellerLocation').textContent = product.seller?.address || 'Location not provided';
            document.getElementById('sellerCoordinates').textContent = product.seller?.coordinates || 'Coordinates not available';
            
            new bootstrap.Modal(document.getElementById('productModal')).show();
        }

        function showLoginModal() {
            closeModals();
            document.getElementById('loginModal').classList.add('show');
        }

        function showSignupModal() {
            closeModals();
            document.getElementById('signupModal').classList.add('show');
        }
        
        function closeModals() {
            document.getElementById('loginModal').classList.remove('show');
            document.getElementById('signupModal').classList.remove('show');
        }
        
        function switchToSignup() {
            closeModals();
            showSignupModal();
        }
        
        function switchToLogin() {
            closeModals();
            showLoginModal();
        }

        function handleLogin(e) {
            e.preventDefault();
            showLoading('Signing you in...');
            const formData = new FormData(e.target);
            formData.append('action', 'login');
            fetch('user_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    currentUser = data.data;
                    localStorage.setItem('currentUser', JSON.stringify(currentUser));
                    closeModals();
                    showDashboard();
                    if (currentUser.role === 'seller') {
                        window.location.href = 'seller.php';
                    }
                    e.target.reset();
                } else {
                    if (data.message === 'No account found with that email.') {
                        showError('loginError', 'No account found. Please sign up to continue.');
                        setTimeout(() => {
                            closeModals();
                            showSignupModal();
                        }, 2000);
                    } else {
                        showError('loginError', data.message);
                    }
                }
            })
            .catch(() => {
                hideLoading();
                showError('loginError', 'Server error. Please try again.');
            });
        }

        function handleSignup(e) {
            e.preventDefault();
            showLoading('Creating your account...');
            const formData = new FormData(e.target);
            formData.append('action', 'signup');
            fetch('user_api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    closeModals();
                    alert('Account created successfully!');
                    showLoginModal();
                    e.target.reset();
                } else {
                    showError('signupError', data.message);
                }
            })
            .catch(() => {
                hideLoading();
                showError('signupError', 'Server error. Please try again.');
            });
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.classList.remove('d-none');
            setTimeout(() => {
                errorElement.classList.add('d-none');
            }, 5000);
        }

        function showDashboard() {
            document.getElementById('navButtons').classList.add('d-none');
            document.getElementById('dashboardNav').classList.remove('d-none');
            document.getElementById('dashboardNav').classList.add('d-flex');
            document.getElementById('userName').textContent = currentUser.name;
            
            if (currentUser.role === 'seller') {
                document.getElementById('roleSwitcherItem').style.display = 'block';
                document.getElementById('roleSwitcherDivider').style.display = 'block';
            }
        }

        function logout() {
            showLoading('Signing you out...');
            
            setTimeout(() => {
                currentUser = null;
                localStorage.removeItem('currentUser');
                document.getElementById('navTitle').textContent = 'GreenKart';
                document.getElementById('navButtons').classList.remove('d-none');
                document.getElementById('dashboardNav').classList.add('d-none');
                document.getElementById('dashboardNav').classList.remove('d-flex');
                document.getElementById('roleSwitcherItem').style.display = 'none';
                document.getElementById('roleSwitcherDivider').style.display = 'none';
                hideLoading();
            }, 1000);
        }

        function showLoading(message = 'Loading...') {
            document.getElementById('loadingText').textContent = message;
            document.getElementById('loadingOverlay').classList.add('show');
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('show');
        }

        function showDeleteAccountConfirm() {
            if (confirm('Are you sure you want to delete your account? This will also delete all your products.')) {
                if (confirm('This will permanently delete all your data. Are you absolutely sure?')) {
                    deleteAccount();
                }
            }
        }

        function deleteAccount() {
            showLoading('Deleting your account...');
            
            setTimeout(() => {
                // Remove user from registered users
                const registeredUsers = JSON.parse(localStorage.getItem('plantHubUsers') || '[]');
                const updatedUsers = registeredUsers.filter(user => user.email !== currentUser.email);
                localStorage.setItem('plantHubUsers', JSON.stringify(updatedUsers));
                
                // Remove all products by this seller
                const allProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
                const updatedProducts = allProducts.filter(product => product.sellerId !== currentUser.email);
                localStorage.setItem('sellerProducts', JSON.stringify(updatedProducts));
                
                // Clear current user session
                currentUser = null;
                localStorage.removeItem('currentUser');
                
                hideLoading();
                alert('Your account and all products have been successfully deleted.');
                window.location.href = 'index.php';
            }, 1500);
        }

        // Listen for storage changes to update products in real-time
        window.addEventListener('storage', function(e) {
            if (e.key === 'sellerProducts') {
                renderProducts();
            }
        });
    </script>
</body>
</html>
