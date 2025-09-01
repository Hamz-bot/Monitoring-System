<?php
require_once 'config.php';
require_once 'auth.php';
requireAuth(); // This will redirect to login if not authenticated
// Start output buffering to prevent headers already sent error
ob_start();
// Include database configuration
require_once 'config.php';

// Functions to get live stream statistics
function getActiveStreams($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM live_streams WHERE status = 'active'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    } catch (Exception $e) {
        return 0;
    }
}

function getTotalStreams($conn) {
    try {
        $sql = "SELECT COUNT(*) as total FROM live_streams";
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
    <title>Live Stream | WordPress Manager Dashboard</title>
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
            --online-green: #4CAF50;
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
        
        .video-container {
            position: relative;
            width: 100%;
            height: 0;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            background-color: #000;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .video-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        .video-placeholder i {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .video-controls {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .user-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .user-card {
            background-color: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            border: 2px solid transparent;
        }
        
        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .user-card.active {
            border-color: var(--primary);
        }
        
        .user-card.online {
            border-color: var(--online-green);
        }
        
        .user-card.online::before {
            content: '';
            position: absolute;
            top: 10px;
            right: 10px;
            width: 12px;
            height: 12px;
            background-color: var(--online-green);
            border-radius: 50%;
            box-shadow: 0 0 0 2px white;
        }
        
        .user-card.streaming {
            border-color: var(--info);
        }
        
        .user-card.streaming::after {
            content: 'LIVE';
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--danger);
            color: white;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .user-avatar-small {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 10px;
            border: 2px solid #e0e0e0;
        }
        
        .user-card.online .user-avatar-small {
            border-color: var(--online-green);
        }
        
        .user-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .user-status {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .user-org {
            font-size: 0.8rem;
            color: #6c757d;
        }
        
        .live-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            z-index: 9999;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .live-screen.active {
            display: flex;
        }
        
        .live-screen-content {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            max-width: 90%;
            max-height: 90%;
            position: relative;
        }
        
        .live-screen-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .live-screen-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .live-screen-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }
        
        .live-screen-close:hover {
            color: var(--danger);
        }
        
        .live-screen-body {
            text-align: center;
        }
        
        .live-screen-image {
            width: 100%;
            max-width: 800px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .live-screen-info {
            display: flex;
            justify-content: space-around;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
        }
        
        .live-screen-stat {
            text-align: center;
        }
        
        .live-screen-stat-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--primary);
        }
        
        .live-screen-stat-label {
            font-size: 0.8rem;
            color: #6c757d;
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
                            <li><a class="dropdown-item" href="screenshots.php"><i class="fas fa-camera"></i> Screenshots</a></li>
                            <li><a class="dropdown-item active" href="live_stream.php"><i class="fas fa-video"></i> Live Stream</a></li>
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
                    <h2>Live Stream</h2>
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
                
                <!-- Live Stream Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-video"></i>
                            </div>
                            <h5>Active Streams</h5>
                            <div class="stat-number text-success"><?php echo getActiveStreams($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <h5>Total Streams</h5>
                            <div class="stat-number text-primary"><?php echo getTotalStreams($conn); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card text-center p-3">
                            <div class="card-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Available Users</h5>
                            <div class="stat-number text-info"><?php echo getActiveUsers($conn); ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Video Player -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5>Live Stream Viewer</h5>
                            </div>
                            <div class="card-body">
                                <div class="video-container">
                                    <div class="video-placeholder">
                                        <i class="fas fa-video-slash"></i>
                                        <h4>Select a user to view their screen</h4>
                                        <p>Click on any user below to start viewing</p>
                                    </div>
                                </div>
                                <div class="video-controls">
                                    <button class="btn btn-wp" id="startStreamBtn" disabled>
                                        <i class="fas fa-play"></i> Start Stream
                                    </button>
                                    <button class="btn btn-outline-danger" id="stopStreamBtn" disabled>
                                        <i class="fas fa-stop"></i> Stop Stream
                                    </button>
                                    <button class="btn btn-outline-primary" id="recordStreamBtn" disabled>
                                        <i class="fas fa-circle"></i> Record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Selection -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5>All Users</h5>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary btn-sm" onclick="refreshUsers()">
                                        <i class="fas fa-sync-alt"></i> Refresh
                                    </button>
                                    <button class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-broadcast-tower"></i> Start All
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="user-grid">
                                    <?php
                                    // Fetch all users with their activation status and streaming status
                                    $sql = "SELECT u.*, ls.status as stream_status 
                                            FROM users u 
                                            LEFT JOIN live_streams ls ON u.id = ls.user_id AND ls.status = 'active'
                                            ORDER BY u.name";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            $statusClass = '';
                                            $statusText = '';
                                            $extraClass = '';
                                            
                                            if ($row['activation'] == 1) {
                                                // User is active (online)
                                                if ($row['stream_status'] == 'active') {
                                                    // User is currently streaming
                                                    $statusClass = 'streaming';
                                                    $statusText = 'Streaming';
                                                    $extraClass = 'streaming';
                                                } else {
                                                    // User is online but not streaming
                                                    $statusClass = 'online';
                                                    $statusText = 'Online';
                                                    $extraClass = 'online';
                                                }
                                            } else {
                                                // User is inactive (offline)
                                                $statusClass = 'offline';
                                                $statusText = 'Offline';
                                            }
                                            
                                            echo '<div class="user-card ' . $extraClass . '" onclick="viewLiveScreen(\'' . htmlspecialchars($row['name']) . '\', ' . $row['id'] . ', \'' . $statusClass . '\')">';
                                            echo '<img src="https://ui-avatars.com/api/?name=' . urlencode($row['name']) . '&background=4b6cb7&color=fff" class="user-avatar-small" alt="User">';
                                            echo '<div class="user-name">' . htmlspecialchars($row['name']) . '</div>';
                                            echo '<div class="user-status">' . $statusText . '</div>';
                                            echo '<div class="user-org">' . htmlspecialchars($row['org']) . '</div>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo '<div class="col-12 text-center py-5">No users found</div>';
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

    <!-- Live Screen Modal -->
    <div class="live-screen" id="liveScreen">
        <div class="live-screen-content">
            <div class="live-screen-header">
                <div class="live-screen-title" id="liveScreenTitle">User Screen</div>
                <button class="live-screen-close" onclick="closeLiveScreen()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="live-screen-body">
                <img src="" alt="Live Screen" class="live-screen-image" id="liveScreenImage">
                <div class="live-screen-info">
                    <div class="live-screen-stat">
                        <div class="live-screen-stat-value" id="viewerCount">0</div>
                        <div class="live-screen-stat-label">Viewers</div>
                    </div>
                    <div class="live-screen-stat">
                        <div class="live-screen-stat-value" id="streamQuality">HD</div>
                        <div class="live-screen-stat-label">Quality</div>
                    </div>
                    <div class="live-screen-stat">
                        <div class="live-screen-stat-value" id="streamDuration">00:00</div>
                        <div class="live-screen-stat-label">Duration</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        let selectedUser = null;
        let selectedUserId = null;
        let selectedUserStatus = null;
        let streamStartTime = null;
        let viewerCount = 0;
        let durationInterval = null;

        function viewLiveScreen(userName, userId, userStatus) {
            selectedUser = userName;
            selectedUserId = userId;
            selectedUserStatus = userStatus;
            
            // Update the live screen title
            document.getElementById('liveScreenTitle').textContent = userName + ' - Live Screen';
            
            // Set the live screen image
            const imageUrl = 'https://picsum.photos/seed/' + userName + '/800/450.jpg';
            document.getElementById('liveScreenImage').src = imageUrl;
            
            // Show the live screen modal
            document.getElementById('liveScreen').classList.add('active');
            
            // Start the stream
            startStream();
            
            // Update viewer count (simulate)
            viewerCount = Math.floor(Math.random() * 10) + 1;
            document.getElementById('viewerCount').textContent = viewerCount;
            
            // Simulate viewer count changes
            setInterval(() => {
                if (Math.random() > 0.7) {
                    viewerCount += Math.floor(Math.random() * 3) - 1;
                    if (viewerCount < 1) viewerCount = 1;
                    document.getElementById('viewerCount').textContent = viewerCount;
                }
            }, 5000);
        }

        function closeLiveScreen() {
            // Hide the live screen modal
            document.getElementById('liveScreen').classList.remove('active');
            
            // Stop the stream
            stopStream();
            
            // Clear intervals
            if (durationInterval) {
                clearInterval(durationInterval);
            }
        }

        function startStream() {
            streamStartTime = new Date();
            
            // Update video placeholder to show stream is active
            document.querySelector('.video-placeholder').innerHTML = `
                <div class="video-player">
                    <img src="https://picsum.photos/seed/${selectedUser}/800/450.jpg" style="width: 100%; height: auto;">
                    <div class="stream-overlay">
                        <h5>${selectedUser} - Live Stream</h5>
                        <p>Stream started at ${streamStartTime.toLocaleTimeString()}</p>
                    </div>
                </div>
            `;
            
            // Enable controls
            document.getElementById('startStreamBtn').disabled = true;
            document.getElementById('stopStreamBtn').disabled = false;
            document.getElementById('recordStreamBtn').disabled = false;
            
            // Start duration counter
            durationInterval = setInterval(updateDuration, 1000);
        }

        function stopStream() {
            // Update video placeholder to show stream is stopped
            document.querySelector('.video-placeholder').innerHTML = `
                <i class="fas fa-video-slash"></i>
                <h4>Select a user to view their screen</h4>
                <p>Click on any user below to start viewing</p>
            `;
            
            // Disable controls
            document.getElementById('startStreamBtn').disabled = false;
            document.getElementById('stopStreamBtn').disabled = true;
            document.getElementById('recordStreamBtn').disabled = true;
            
            // Reset record button
            const recordBtn = document.getElementById('recordStreamBtn');
            recordBtn.classList.remove('btn-danger');
            recordBtn.classList.add('btn-outline-primary');
            recordBtn.innerHTML = '<i class="fas fa-circle"></i> Record';
        }

        function updateDuration() {
            if (streamStartTime) {
                const now = new Date();
                const diff = Math.floor((now - streamStartTime) / 1000);
                const minutes = Math.floor(diff / 60);
                const seconds = diff % 60;
                document.getElementById('streamDuration').textContent = 
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }
        
        // Start stream button click handler
        document.getElementById('startStreamBtn').addEventListener('click', function() {
            if (selectedUser && selectedUserId) {
                // Send AJAX request to start stream
                fetch('start_stream.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'user_id=' + selectedUserId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        startStream();
                    } else {
                        alert('Failed to start stream: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while starting the stream');
                });
            }
        });
        
        // Stop stream button click handler
        document.getElementById('stopStreamBtn').addEventListener('click', function() {
            if (selectedUserId) {
                // Send AJAX request to stop stream
                fetch('stop_stream.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'user_id=' + selectedUserId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        stopStream();
                    } else {
                        alert('Failed to stop stream: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while stopping the stream');
                });
            }
        });
        
        // Record stream button click handler
        document.getElementById('recordStreamBtn').addEventListener('click', function() {
            // Toggle recording state
            if (this.classList.contains('btn-outline-primary')) {
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-danger');
                this.innerHTML = '<i class="fas fa-stop"></i> Stop Recording';
                
                // Show recording indicator
                const recordingIndicator = document.createElement('div');
                recordingIndicator.innerHTML = '<i class="fas fa-circle" style="color: red;"></i> REC';
                recordingIndicator.style.position = 'absolute';
                recordingIndicator.style.top = '10px';
                recordingIndicator.style.left = '10px';
                recordingIndicator.style.color = 'red';
                recordingIndicator.style.fontWeight = 'bold';
                recordingIndicator.style.animation = 'pulse 1s infinite';
                document.querySelector('.live-screen-header').appendChild(recordingIndicator);
            } else {
                this.classList.remove('btn-danger');
                this.classList.add('btn-outline-primary');
                this.innerHTML = '<i class="fas fa-circle"></i> Record';
                
                // Remove recording indicator
                const recordingIndicator = document.querySelector('.live-screen-header .fa-circle').parentElement;
                if (recordingIndicator) {
                    recordingIndicator.remove();
                }
            }
        });
        
        function refreshUsers() {
            location.reload();
        }
    </script>
</body>
</html>