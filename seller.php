<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenKart Seller - Manage Your Plants</title>
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
        
        .store-bounce {
            animation: storeBounce 2s ease-in-out infinite;
        }
        
        @keyframes storeBounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-8px); }
            60% { transform: translateY(-4px); }
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
            <a class="navbar-brand d-flex align-items-center" href="seller.php">
                <i class="fas fa-leaf text-emerald-600 fs-2 me-2"></i>
                <span class="fw-bold text-emerald-600">GreenKart Seller</span>
            </a>
            <div class="d-flex align-items-center" id="dashboardNav">
                <span class="text-muted me-3">Welcome, <span id="userName"></span>!</span>
                <div class="dropdown">
                    <button class="btn btn-link settings-icon" type="button" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Account Settings">
                        <i class="fas fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                        <li>
                            <a class="dropdown-item" href="index.php">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span>Go to Marketplace</span>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="showDeleteAccountConfirm()"><i class="fas fa-trash me-2"></i>Delete Account</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Seller Dashboard Hero Section -->
    <div class="hero-section text-center py-5 bg-emerald-gradient">
        <div class="container text-center">
            <div class="hero-icon store-bounce">
                <i class="fas fa-store text-white fs-2"></i>
            </div>
            <h1 class="display-4 fw-bold text-dark mb-3">Seller Dashboard</h1>
            <p class="lead text-muted mb-4 mx-auto" style="max-width: 600px;">
                Manage your plant inventory and connect with buyers
            </p>
            
            <button class="btn btn-emerald btn-lg" onclick="showAddProductModal()">
                <i class="fas fa-plus me-2"></i>Add New Product
            </button>
        </div>
    </div>

    <!-- Products Section -->
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">My Products</h2>
                <span class="text-muted">Total: <span id="productCount">0</span> products</span>
            </div>
            
            <div id="noProducts" class="text-center py-5">
                <i class="fas fa-seedling text-muted display-1 mb-3"></i>
                <p class="text-muted fs-5">You haven't added any products yet.</p>
                <button class="btn btn-emerald" onclick="showAddProductModal()">Add Your First Product</button>
            </div>
            
            <div class="row g-4" id="sellerProductsGrid">
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
                        <h5 class="mb-0 fw-bold">GreenKart Seller</h5>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end justify-content-center mt-3 mt-md-0">
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

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal-overlay">
        <div class="modal-container">
            <h2 class="text-center mb-4">Add New Product</h2>
            
            <form id="addProductForm" class="modal-form">
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" class="form-input" placeholder="Enter product name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" class="form-input" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-input" required>
                        <option value="">Select category</option>
                        <option value="indoor">Indoor Plants</option>
                        <option value="outdoor">Outdoor Plants</option>
                        <option value="succulents">Succulents</option>
                        <option value="flowers">Flowers</option>
                        <option value="herbs">Herbs</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Describe your product..." required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <input type="url" name="image" class="form-input" placeholder="https://example.com/image.jpg" required>
                </div>
                
                <h4 class="mt-4 mb-3">Seller Information</h4>
                <div class="form-group">
                    <label class="form-label">Shop/Seller Name</label>
                    <input type="text" name="shopName" class="form-input" placeholder="Your shop name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-input" placeholder="+1 (555) 123-4567" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-textarea" placeholder="Your business address..." required></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Add Product</button>
            </form>
            
            <button type="button" onclick="closeModals()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal-overlay">
        <div class="modal-container">
            <h2 class="text-center mb-4">Edit Product</h2>
            
            <form id="editProductForm" class="modal-form">
                <input type="hidden" name="productId" id="editProductId">
                <div class="form-group">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" id="editProductName" class="form-input" placeholder="Enter product name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" id="editProductPrice" class="form-input" placeholder="0.00" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" id="editProductCategory" class="form-input" required>
                        <option value="">Select category</option>
                        <option value="indoor">Indoor Plants</option>
                        <option value="outdoor">Outdoor Plants</option>
                        <option value="succulents">Succulents</option>
                        <option value="flowers">Flowers</option>
                        <option value="herbs">Herbs</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="editProductDescription" class="form-textarea" placeholder="Describe your product..." required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Image URL</label>
                    <input type="url" name="image" id="editProductImage" class="form-input" placeholder="https://example.com/image.jpg" required>
                </div>
                
                <h4 class="mt-4 mb-3">Seller Information</h4>
                <div class="form-group">
                    <label class="form-label">Shop/Seller Name</label>
                    <input type="text" name="shopName" id="editShopName" class="form-input" placeholder="Your shop name" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" name="phone" id="editPhone" class="form-input" placeholder="+63 (XXX) YYY ZZZZ" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Address</label>
                    <textarea name="address" id="editAddress" class="form-textarea" placeholder="Your business address..." required></textarea>
                </div>
                
                <button type="submit" class="btn-primary">Update Product</button>
            </form>
            
            <button type="button" onclick="closeModals()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
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
        let sellerProducts = [];

        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            // Check for existing user session
            const savedUser = localStorage.getItem('currentUser');
            if (savedUser) {
                currentUser = JSON.parse(savedUser);
                
                // Redirect non-sellers to marketplace
                if (currentUser.role !== 'seller') {
                    window.location.href = 'index.php';
                    return;
                }
                
                showDashboard();
                loadSellerProducts();
            } else {
                // Redirect to marketplace if not logged in
                window.location.href = 'index.php';
                return;
            }
            
            setupEventListeners();
        });

        function setupEventListeners() {
            // Forms
            document.getElementById('addProductForm').addEventListener('submit', handleAddProduct);
            document.getElementById('editProductForm').addEventListener('submit', handleEditProduct);
        }

        function loadSellerProducts() {
            const allProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
            sellerProducts = allProducts.filter(product => product.sellerId === currentUser.email);
            renderSellerProducts();
        }

        function renderSellerProducts() {
            const grid = document.getElementById('sellerProductsGrid');
            const noProducts = document.getElementById('noProducts');
            const productCount = document.getElementById('productCount');
            
            productCount.textContent = sellerProducts.length;
            
            if (sellerProducts.length === 0) {
                grid.innerHTML = '';
                noProducts.classList.remove('d-none');
                return;
            }
            
            noProducts.classList.add('d-none');
            
            grid.innerHTML = sellerProducts.map(product => `
                <div class="col-md-6 col-lg-4">
                    <div class="card product-card h-100">
                        <img src="${product.image}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="text-emerald-600 fw-bold fs-5 mb-2">₱${product.price}</p>
                            <p class="text-muted small mb-2">${product.description.substring(0, 100)}...</p>
                            <span class="badge bg-secondary">${product.category}</span>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-grid gap-2">
                                <button class="btn btn-emerald btn-sm" onclick="showEditProductModal(${product.id})">
                                    <i class="fas fa-edit me-2"></i>Edit Product
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteProduct(${product.id})">
                                    <i class="fas fa-trash me-2"></i>Delete Product
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function showAddProductModal() {
            document.getElementById('addProductModal').classList.add('show');
        }

        function showEditProductModal(productId) {
            const product = sellerProducts.find(p => p.id === productId);
            if (!product) return;
            
            // Pre-fill the form with existing product data
            document.getElementById('editProductId').value = product.id;
            document.getElementById('editProductName').value = product.name;
            document.getElementById('editProductPrice').value = product.price;
            document.getElementById('editProductCategory').value = product.category;
            document.getElementById('editProductDescription').value = product.description;
            document.getElementById('editProductImage').value = product.image;
            document.getElementById('editShopName').value = product.seller.name;
            document.getElementById('editPhone').value = product.seller.phone;
            document.getElementById('editAddress').value = product.seller.address;
            
            document.getElementById('editProductModal').classList.add('show');
        }

        function closeModals() {
            document.getElementById('addProductModal').classList.remove('show');
            document.getElementById('editProductModal').classList.remove('show');
        }

        function handleAddProduct(e) {
            e.preventDefault();
            showLoading('Adding your product...');
            
            const formData = new FormData(e.target);
            
            setTimeout(() => {
                const newProduct = {
                    id: Date.now(),
                    sellerId: currentUser.email,
                    name: formData.get('name'),
                    price: parseFloat(formData.get('price')),
                    category: formData.get('category'),
                    description: formData.get('description'),
                    image: formData.get('image'),
                    seller: {
                        name: formData.get('shopName'),
                        phone: formData.get('phone'),
                        address: formData.get('address'),
                        rating: "New Seller"
                    },
                    dateAdded: new Date().toISOString()
                };
                
                const allProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
                allProducts.push(newProduct);
                localStorage.setItem('sellerProducts', JSON.stringify(allProducts));
                
                hideLoading();
                closeModals();
                loadSellerProducts();
                e.target.reset();
                
                alert('Product added successfully!');
            }, 1500);
        }

        function handleEditProduct(e) {
            e.preventDefault();
            showLoading('Updating your product...');
            
            const formData = new FormData(e.target);
            const productId = parseInt(formData.get('productId'));
            
            setTimeout(() => {
                const allProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
                const productIndex = allProducts.findIndex(product => product.id === productId);
                
                if (productIndex !== -1) {
                    // Update the existing product
                    allProducts[productIndex] = {
                        ...allProducts[productIndex],
                        name: formData.get('name'),
                        price: parseFloat(formData.get('price')),
                        category: formData.get('category'),
                        description: formData.get('description'),
                        image: formData.get('image'),
                        seller: {
                            name: formData.get('shopName'),
                            phone: formData.get('phone'),
                            address: formData.get('address'),
                            rating: allProducts[productIndex].seller.rating // Keep existing rating
                        },
                        lastUpdated: new Date().toISOString()
                    };
                    
                    localStorage.setItem('sellerProducts', JSON.stringify(allProducts));
                    
                    hideLoading();
                    closeModals();
                    loadSellerProducts();
                    
                    alert('Product updated successfully!');
                } else {
                    hideLoading();
                    alert('Error: Product not found.');
                }
            }, 1500);
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                showLoading('Deleting product...');
                
                setTimeout(() => {
                    const allProducts = JSON.parse(localStorage.getItem('sellerProducts') || '[]');
                    const updatedProducts = allProducts.filter(product => product.id !== productId);
                    localStorage.setItem('sellerProducts', JSON.stringify(updatedProducts));
                    
                    hideLoading();
                    loadSellerProducts();
                    
                    alert('Product deleted successfully!');
                }, 1000);
            }
        }

        function showDashboard() {
            document.getElementById('userName').textContent = currentUser.name;
        }

        function logout() {
            showLoading('Signing you out...');
            
            setTimeout(() => {
                currentUser = null;
                localStorage.removeItem('currentUser');
                hideLoading();
                window.location.href = 'index.php';
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
    </script>
</body>
</html>
