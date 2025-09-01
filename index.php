<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        <?php
        require_once 'config.php';
        require_once 'auth.php';
        requireAuth(); // This will redirect to login if not authenticated
        ?>
        :root {
            --primary: #4b6cb7;
            --secondary: #182848;
            --light: #f8f9fa;
            --dark: #343a40;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            color: white;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: bold;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .dashboard-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--primary);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        
        .wp-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        
        .wp-table thead {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
        }
        
        .btn-wp {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            color: white;
            border: none;
        }
        
        .btn-wp:hover {
            background: linear-gradient(to right, var(--secondary), var(--primary));
            color: white;
        }
        
        .header {
            background-color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .notification-badge {
            position: relative;
        }
        
        .notification-badge .badge {
            position: absolute;
            top: -5px;
            right: -5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-none d-md-block">
                <div class="text-center mb-4">
                    <h3><i class="fab fa-wordpress"></i> WP Manager</h3>
                </div>
                <ul class="nav flex-column" id="sidebarLinks">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                  <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-user-lock"></i> User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-plug"></i> Plugin Updates
                        </a>
                    </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-plus-circle"></i> Add Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-key"></i> Password Manager
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="header d-flex justify-content-between align-items-center">
                    <h2>WordPress Dashboard</h2>
                    <div class="d-flex">
                        <div class="notification-badge mx-3">
                            <i class="fas fa-bell fa-lg text-muted"></i>
                            <span class="badge bg-danger">3</span>
                        </div>
                        <div class="user-info">
                            <img src="https://ui-avatars.com/api/?name=Admin+User&background=4b6cb7&color=fff" class="rounded-circle" width="40" height="40" alt="User">
                            <span class="ms-2">Admin User</span>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fab fa-wordpress-simple"></i>
                            </div>
                            <h5>WordPress Sites</h5>
                            <div class="stat-number text-primary">5</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-plug"></i>
                            </div>
                            <h5>Plugin Updates</h5>
                            <div class="stat-number text-warning">12</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <h5>Users</h5>
                            <div class="stat-number text-info">42</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5>Security</h5>
                            <div class="stat-number text-success">98%</div>
                        </div>
                    </div>
                </div>

                <!-- Add WordPress Site Form -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5>Add WordPress Site</h5>
                            </div>
                            <div class="card-body">
                                <form id="wpSiteForm" method="POST">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="siteName" class="form-label">Site Name</label>
                                                <input type="text" class="form-control" id="siteName" name="siteName" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="siteUrl" class="form-label">Site URL</label>
                                                <input type="url" class="form-control" id="siteUrl" name="siteUrl" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adminUser" class="form-label">Admin User</label>
                                                <input type="text" class="form-control" id="adminUser" name="adminUser" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-end">
                                            <button type="submit" class="btn btn-wp w-100">Add Site</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- WordPress Sites Table -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5>Your WordPress Sites</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover wp-table">
                                        <thead>
                                            <tr>
                                                <th>Site Name</th>
                                                <th>URL</th>
                                                <th>Admin User</th>
                                                <th>Plugins</th>
                                                <th>Users</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>My Blog</td>
                                                <td>https://myblog.com</td>
                                                <td>admin</td>
                                                <td><span class="badge bg-warning">3 updates</span></td>
                                                <td>5</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-sync-alt"></i> Update
                                                    </button>
                                                    <button class="btn btn-sm btn-info">
                                                        <i class="fas fa-key"></i> Password
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Business Site</td>
                                                <td>https://business.com</td>
                                                <td>manager</td>
                                                <td><span class="badge bg-danger">5 updates</span></td>
                                                <td>12</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-sync-alt"></i> Update
                                                    </button>
                                                    <button class="btn btn-sm btn-info">
                                                        <i class="fas fa-key"></i> Password
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>E-commerce Store</td>
                                                <td>https://store.com</td>
                                                <td>admin</td>
                                                <td><span class="badge bg-success">Updated</span></td>
                                                <td>25</td>
                                                <td><span class="badge bg-warning">Needs attention</span></td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary">
                                                        <i class="fas fa-sync-alt"></i> Update
                                                    </button>
                                                    <button class="btn btn-sm btn-info">
                                                        <i class="fas fa-key"></i> Password
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- End Main Content -->
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    
    <script>
        // Sidebar active highlighting
        const currentPage = window.location.pathname.split('/').pop();
        document.querySelectorAll('#sidebarLinks .nav-link').forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            }
        });

        // Form submission handling
        document.getElementById('wpSiteForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const siteName = document.getElementById('siteName').value;
            const siteUrl = document.getElementById('siteUrl').value;
            alert(`Site "${siteName}" (${siteUrl}) has been added successfully!`);
            this.reset();
        });

        // Simulate update actions
        document.querySelectorAll('.btn-primary').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const siteName = row.querySelector('td:first-child').textContent;
                alert(`Updating plugins for ${siteName}...`);
                setTimeout(() => {
                    alert(`Plugins for ${siteName} have been updated successfully!`);
                    const badge = row.querySelector('.badge');
                    badge.className = 'badge bg-success';
                    badge.textContent = 'Updated';
                }, 1500);
            });
        });

        // Simulate password change actions
        document.querySelectorAll('.btn-info').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const siteName = row.querySelector('td:first-child').textContent;
                alert(`Changing password for ${siteName}...`);
                setTimeout(() => {
                    alert(`Password for ${siteName} has been updated successfully!`);
                }, 1500);
            });
        });
    </script>
</body>
</html>
