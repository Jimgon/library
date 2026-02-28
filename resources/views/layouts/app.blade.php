<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: #fff;
            color: #000;
        }
        .sidebar {
            min-height: 100vh;
            background: #fff;
            color: #111;
            width: 220px;
            position: fixed;
            top: 0;
            left: 0;
            border-right: 1px solid #e5e7eb;
            padding-top: 1.5rem;
            display: flex;
            flex-direction: column;
        }
        .sidebar .navbar-brand {
            color: #111 !important;
            font-size: 1.3rem;
            margin-bottom: 2rem;
            display: block;
            text-align: left;
            font-weight: 700;
            letter-spacing: -0.5px;
            padding-left: 1.25rem;
        }
        .sidebar .nav-link {
            color: #111 !important;
            padding: 0.65rem 1.25rem;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            transition: background 0.15s, color 0.15s;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link i {
            font-size: 1.1rem;
            opacity: 0.85;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #bfdbfe !important;
            color:  #2563eb!important;
        }
        .sidebar .nav-link.text-danger {
            color: #dc2626 !important;
        }
        .sidebar .nav-link.text-danger:hover {
            background: #fee2e2 !important;
            color: #b91c1c !important;
        }
        .sidebar .mt-auto {
            margin-top: auto;
        }
        .main-content {
            margin-left: 220px;
            padding: 2rem 1rem;
        }
        .navbar {
            background: #000 !important;
        }
        .navbar .navbar-brand,
        .navbar .nav-link {
            color: #fff !important;
        }
        .navbar .nav-link:hover {
            color: #ccc !important;
        }
        .btn-primary {
            background-color: #000 !important;
            border-color: #000 !important;
            color: #fff !important;
        }
        .btn-primary:hover {
            background-color: #222 !important;
            border-color: #222 !important;
        }
        #toast-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2000;
        }
        .toast {
            opacity: 0;
            transform: translateY(-50px);
            animation: slideIn 0.5s forwards, fadeOut 0.5s 3.5s forwards;
        }
        @keyframes slideIn {
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeOut {
            to { opacity: 0; }
        }
        .btn-close {
            filter: invert(1);
        }
         /* Compact, shadcn-style pagination */
    /* Compact, shadcn-style pagination (auto-hide friendly) */
.pagination {
    font-size: 0.1rem;
    
}

.pagination .page-link {
    padding: 0.25rem 0.55rem;
    border-radius: 0.375rem;
    color: #111;
    transition: all 0.15s ease;
}

.pagination .page-link:hover {
    background-color: #111;
    border-color: #111;
    color: #fff;
}

.pagination .page-item.active .page-link {
    background-color: #111;
    border-color: #111;
    color: #fff;
}

.pagination .page-item.disabled .page-link {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column p-3">
    <a class="navbar-brand mb-4 fw-bold" href="{{ route('books.index') }}">
        <i class="fas fa-book-open"></i> SNHS Library
    </a>

    <nav class="nav flex-column grow">

        <a class="nav-link{{ request()->routeIs('dashboard') ? ' active' : '' }}"
           href="{{ route('dashboard') }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>

        <button
            type="button"
            class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('books.*') ? ' active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#booksMenu"
            aria-expanded="{{ request()->routeIs('books.*') ? 'true' : 'false' }}"
            aria-controls="booksMenu"
        >
            <span>
                <i class="fas fa-book"></i> Books
            </span>
            <i class="fas fa-chevron-down small"></i>
        </button>

        <div class="collapse ps-3 {{ request()->routeIs('books.*') ? 'show' : '' }}" id="booksMenu">
            <a class="nav-link{{ request()->routeIs('books.index') ? ' active' : '' }}"
               href="{{ route('books.index') }}">
                <i class="fas fa-book"></i> Book     Inventory
            </a>

            <a class="nav-link{{ request()->routeIs('books.catalog') ? ' active' : '' }}"
               href="{{ route('books.catalog') }}">
                <i class="fas fa-archive"></i> Book Catalog
            </a>

        </div>
       
        <!-- Users (Collapsible) -->
        <button
            type="button"
            class="nav-link d-flex justify-content-between align-items-center
                   {{ request()->routeIs('users.*') ? ' active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#usersMenu"
            aria-expanded="{{ request()->routeIs('users.*') ? 'true' : 'false' }}"
            aria-controls="usersMenu"
        >
            <span>
                <i class="fas fa-users"></i> Profiles
            </span>
            <i class="fas fa-chevron-down small"></i>
        </button>

        <div
            class="collapse ps-3 {{ request()->routeIs('users.*') ? 'show' : '' }}"
            id="usersMenu"
        >
            <a class="nav-link{{ request()->routeIs('users.index') ? ' active' : '' }}"
               href="{{ route('users.index') }}">
                <i class="fas fa-user-graduate"></i> Students
            </a>

                <a class="nav-link{{ request()->routeIs('teachers.index') ? ' active' : '' }}"
                    href="{{ route('teachers.index') }}">
                <i class="fas fa-chalkboard-teacher"></i> Teachers
            </a>
        </div>

        <!-- Transactions (Collapsible) -->
        <button
            type="button"
            class="nav-link d-flex justify-content-between align-items-center
                   {{ request()->routeIs('borrow.*') ? ' active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#transactionsMenu"
            aria-expanded="{{ request()->routeIs('borrow.*') ? 'true' : 'false' }}"
            aria-controls="transactionsMenu"
        >
            <span>
                <i class="fas fa-exchange-alt"></i> Transactions
            </span>
            <i class="fas fa-chevron-down small"></i>
        </button>

        <div
            class="collapse ps-3 {{ request()->routeIs('borrow.*') ? 'show' : '' }}"
            id="transactionsMenu"
        >
            <a class="nav-link{{ request()->routeIs('borrow.create') ? ' active' : '' }}"
               href="{{ route('borrow.create') }}">
                <i class="fas fa-hand-holding"></i> Borrow Book
            </a>

  <a class="nav-link{{ request()->routeIs('borrow.distribute') ? ' active' : '' }}"
               href="{{ route('borrow.distribute') }}">
                <i class="fas fa-box-open"></i> Borrow for Distribution
            </a>

            <a class="nav-link{{ request()->routeIs('borrow.return.index') ? ' active' : '' }}"
               href="{{ route('borrow.return.index') }}">
                <i class="fas fa-undo"></i> Return Book
            </a>

            
        </div>

        @if(auth()->user()->role === 'admin')
            <a class="nav-link{{ request()->routeIs('staff.*') ? ' active' : '' }}"
               href="{{ route('staff.index') }}">
                <i class="fas fa-users"></i> User Management
            </a>
        @endif


 <!-- Reports -->
    <a class="nav-link{{ request()->routeIs('reports') ? ' active' : '' }}"
           href="{{ route('reports') }}">
            <i class="fas fa-chart-line"></i> Reports
        </a>


        <!-- Utilities (Collapsible) -->
        <button
            type="button"
            class="nav-link d-flex justify-content-between align-items-center{{ request()->routeIs('utilities.*') ? ' active' : '' }}"
            data-bs-toggle="collapse"
            data-bs-target="#utilitiesMenu"
            aria-expanded="{{ request()->routeIs('utilities.*') ? 'true' : 'false' }}"
            aria-controls="utilitiesMenu"
        >
            <span>
                <i class="fas fa-tools"></i> Utilities
            </span>
            <i class="fas fa-chevron-down small"></i>
        </button>
        <div
            class="collapse ps-3 {{ request()->routeIs('utilities.*') ? 'show' : '' }}"
            id="utilitiesMenu"
        >
            <a class="nav-link{{ request()->routeIs('utilities.logs') ? ' active' : '' }}"
               href="{{ route('utilities.logs') }}">
                <i class="fas fa-list"></i> Activity Logs
            </a>
            <a class="nav-link{{ request()->routeIs('utilities.archive') ? ' active' : '' }}"
               href="{{ route('utilities.archive') }}">
                <i class="fas fa-archive"></i> Archive
            </a>
            <a class="nav-link" href="{{ route('utilities.backups') }}">
                <i class="fas fa-database"></i> Database Backups
            </a>
           
        </div>
        <script>
            const backupFormSidebar = document.getElementById('backupFormSidebar');
            if (backupFormSidebar) {
                backupFormSidebar.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if(confirm("Are you sure you want to backup the entire database?")) {
                        backupFormSidebar.submit();
                    }
                });
            }
        </script>
        <!-- End Utilities Dropdown -->

        <div class="mt-auto">
            <a href="{{ route('logout') }}"
               class="nav-link text-danger"
               onclick="return confirm('Are you sure you want to log out?');">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>
</div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container">
        @if(session('success'))
            <div class="toast align-items-center text-white bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Bootstrap toasts
        document.addEventListener('DOMContentLoaded', () => {
            const toastElList = [].slice.call(document.querySelectorAll('.toast'))
            toastElList.map((toastEl) => {
                return new bootstrap.Toast(toastEl, { delay: 4000 }).show()
            })
        });
    </script>
    @include('components.confirm-modal')
    @stack('scripts')
</body>
</html>
