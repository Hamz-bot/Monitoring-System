<?php
require_once 'config.php';
require_once 'auth.php';
requireAuth(); // This will redirect to login if not authenticated
// Start output buffering to prevent headers already sent error
ob_start();
// Include database configuration
require_once 'config.php';
// Functions to get screenshot statistics
function getTotalScreenshots($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM screenshots";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}
function getTodayScreenshots($conn) {
    try {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(*) as total FROM screenshots WHERE DATE(timestamp) = '$today'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?php echo $_SESSION['user_id']; ?>">
    <title>Screenshots | WordPress Manager Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }
        
        .action-btn {
            border: none;
            background: none;
            color: #6c757d;
            padding: 5px;
            margin: 0 2px;
        }
        
        .action-btn:hover {
            color: var(--primary);
        }
        
        .screenshot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .screenshot-card {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        
        .screenshot-card:hover {
            transform: translateY(-5px);
        }
        
        .screenshot-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .screenshot-img:hover {
            transform: scale(1.05);
        }
        
        .screenshot-info {
            padding: 15px;
            background-color: white;
        }
        
        .screenshot-time {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .screenshot-user {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        /* Dropdown menu styling */
        .dropdown-menu {
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            border: none;
            padding: 0;
            margin: 0;
            border-radius: 0;
        }
        
        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px;
            margin: 0;
        }
        
        .dropdown-item:hover, .dropdown-item:focus {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .dropdown-item i {
            margin-right: 10px;
        }
        
        .dropdown-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin: 0;
        }
        
        /* Modal for screenshot viewing */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
        }
        
        .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 90%;
            margin-top: 50px;
        }
        
        .modal-close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .modal-close:hover {
            color: #bbb;
        }
        
        /* Capture method badge styling */
        .badge-manual {
            background-color: #6c757d;
        }
        
        .badge-automatic {
            background-color: #17a2b8;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="live_tracking.php" id="liveTrackingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-satellite-dish"></i> Live Tracking
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="liveTrackingDropdown">
                            <li><a class="dropdown-item active" href="screenshots.php"><i class="fas fa-camera"></i> Screenshots</a></li>
                            <li><a class="dropdown-item" href="live_stream.php"><i class="fas fa-video"></i> Live Stream</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-tasks"></i> Projects & Tasks
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-file-invoice-dollar"></i> Pricing & Billing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-clock"></i> Time Claim
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-hourglass-half"></i> Time Entry
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-calendar-alt"></i> Holiday
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
                    <h2>Screenshots</h2>
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
                
                <!-- Screenshot Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-images"></i>
                            </div>
                            <h5>Total Screenshots</h5>
                            <div class="stat-number text-primary"><?php echo getTotalScreenshots($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <h5>Today's Screenshots</h5>
                            <div class="stat-number text-success"><?php echo getTodayScreenshots($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Active Monitors</h5>
                            <div class="stat-number text-info"><?php echo getActiveUsers($conn); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Upload Screenshot Form -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5>Upload Screenshot</h5>
                            </div>
                            <div class="card-body">
                                <form action="upload_screenshot.php" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="user_id" class="form-label">User</label>
                                                <select class="form-select" id="user_id" name="user_id" required>
                                                    <option value="">Select User</option>
                                                    <?php
                                                    $sql = "SELECT id, name FROM users WHERE activation = 1 ORDER BY name";
                                                    $users = $conn->query($sql);
                                                    while($user = $users->fetch_assoc()) {
                                                        echo '<option value="' . $user['id'] . '">' . $user['name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="screenshot" class="form-label">Screenshot</label>
                                                <input type="file" class="form-control" id="screenshot" name="screenshot" accept="image/*" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="submit" class="btn btn-wp w-100">Upload</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters and Controls -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" action="">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select class="form-select" name="user_id">
                                                <option value="">All Users</option>
                                                <?php
                                                $sql = "SELECT id, name FROM users ORDER BY name";
                                                $users = $conn->query($sql);
                                                $selectedUser = isset($_GET['user_id']) ? $_GET['user_id'] : '';
                                                while($user = $users->fetch_assoc()) {
                                                    $selected = ($user['id'] == $selectedUser) ? 'selected' : '';
                                                    echo '<option value="' . $user['id'] . '" ' . $selected . '>' . $user['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="date" class="form-control" name="date" value="<?php echo isset($_GET['date']) ? $_GET['date'] : ''; ?>">
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" name="time_range">
                                                <option value="">All Time</option>
                                                <option value="1" <?php echo (isset($_GET['time_range']) && $_GET['time_range'] == '1') ? 'selected' : ''; ?>>Last Hour</option>
                                                <option value="24" <?php echo (isset($_GET['time_range']) && $_GET['time_range'] == '24') ? 'selected' : ''; ?>>Last 24 Hours</option>
                                                <option value="168" <?php echo (isset($_GET['time_range']) && $_GET['time_range'] == '168') ? 'selected' : ''; ?>>Last 7 Days</option>
                                                <option value="720" <?php echo (isset($_GET['time_range']) && $_GET['time_range'] == '720') ? 'selected' : ''; ?>>Last 30 Days</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-select" name="capture_method">
                                                <option value="">All Methods</option>
                                                <option value="manual" <?php echo (isset($_GET['capture_method']) && $_GET['capture_method'] == 'manual') ? 'selected' : ''; ?>>Manual</option>
                                                <option value="automatic" <?php echo (isset($_GET['capture_method']) && $_GET['capture_method'] == 'automatic') ? 'selected' : ''; ?>>Automatic</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-flex gap-2">
                                            <button type="submit" class="btn btn-wp w-50">Filter</button>
                                            <a href="screenshots.php" class="btn btn-outline-primary w-50">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Screenshots Grid -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5>Recent Screenshots</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="refreshScreenshots()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-download"></i> Export All
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="screenshot-grid">
                                    <?php
                                    // Get filter values
                                    $userFilter = isset($_GET['user_id']) ? $_GET['user_id'] : '';
                                    $dateFilter = isset($_GET['date']) ? $_GET['date'] : '';
                                    $captureMethodFilter = isset($_GET['capture_method']) ? $_GET['capture_method'] : '';
                                    
                                    // Build query
                                    $sql = "SELECT s.*, u.name, u.org 
                                            FROM screenshots s 
                                            JOIN users u ON s.user_id = u.id 
                                            WHERE 1=1";
                                    
                                    if (!empty($userFilter)) {
                                        $sql .= " AND s.user_id = " . intval($userFilter);
                                    }
                                    
                                    if (!empty($dateFilter)) {
                                        $sql .= " AND DATE(s.timestamp) = '" . mysqli_real_escape_string($conn, $dateFilter) . "'";
                                    }
                                    
                                    // Add time range filter
                                    if (!empty($_GET['time_range'])) {
                                        $hours = intval($_GET['time_range']);
                                        $sql .= " AND s.timestamp >= DATE_SUB(NOW(), INTERVAL $hours HOUR)";
                                    }
                                    
                                    // Add capture method filter
                                    if (!empty($captureMethodFilter)) {
                                        $sql .= " AND s.capture_method = '" . mysqli_real_escape_string($conn, $captureMethodFilter) . "'";
                                    }
                                    
                                    $sql .= " ORDER BY s.timestamp DESC LIMIT 20";
                                    
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            echo '<div class="screenshot-card">';
                                            echo '<img src="' . htmlspecialchars($row['image_path']) . '" class="screenshot-img" alt="Screenshot" onclick="viewScreenshot(this.src)">';
                                            echo '<div class="screenshot-info">';
                                            echo '<div class="screenshot-user">' . htmlspecialchars($row['name']) . '</div>';
                                            echo '<div class="screenshot-time">' . date('M j, Y, g:i A', strtotime($row['timestamp'])) . '</div>';
                                            
                                            // Add capture method badge
                                            $captureBadgeClass = ($row['capture_method'] == 'automatic') ? 'badge-automatic' : 'badge-manual';
                                            echo '<span class="badge ' . $captureBadgeClass . '">' . ucfirst($row['capture_method']) . '</span>';
                                            
                                            echo '<div class="mt-2">';
                                            echo '<button class="action-btn" onclick="viewScreenshot(\'' . htmlspecialchars($row['image_path']) . '\')"><i class="fas fa-eye"></i></button>';
                                            echo '<button class="action-btn" onclick="downloadScreenshot(\'' . htmlspecialchars($row['image_path']) . '\')"><i class="fas fa-download"></i></button>';
                                            echo '<button class="action-btn" onclick="deleteScreenshot(' . $row['id'] . ')"><i class="fas fa-trash"></i></button>';
                                            echo '</div>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<div class="col-12 text-center py-5">No screenshots found</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Screenshot Modal -->
    <div id="screenshotModal" class="modal">
        <span class="modal-close">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="screen_capture.js"></script>
    <script>
        // Fix dropdown menu links
        document.addEventListener('DOMContentLoaded', function() {
            // Get all dropdown items
            const dropdownItems = document.querySelectorAll('.dropdown-item');
            
            // Add click event listener to each dropdown item
            dropdownItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    // Prevent default behavior
                    e.preventDefault();
                    
                    // Get the href attribute
                    const href = this.getAttribute('href');
                    
                    // Navigate to the href
                    window.location.href = href;
                });
            });
        });
        // Screenshot modal functionality
        const modal = document.getElementById("screenshotModal");
        const modalImg = document.getElementById("modalImage");
        const closeBtn = document.getElementsByClassName("modal-close")[0];
        function viewScreenshot(src) {
            modal.style.display = "block";
            modalImg.src = src;
        }
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        function downloadScreenshot(src) {
            const link = document.createElement('a');
            link.href = src;
            link.download = src.substring(src.lastIndexOf('/') + 1);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        function deleteScreenshot(id) {
            if (confirm('Are you sure you want to delete this screenshot?')) {
                window.location.href = 'delete_screenshot.php?id=' + id;
            }
        }
        function refreshScreenshots() {
            location.reload();
        }
    </script>
</body>
</html>