<?php
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: register.html");
    exit();
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9fafb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }
        
        .container {
            width: 100%;
            max-width: 480px;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 24px;
            border: 4px solid #f0f4f8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        
        h1 {
            margin-bottom: 24px;
            font-weight: 600;
            color: #111;
            font-size: 24px;
        }
        
        .user-info {
            margin-bottom: 30px;
        }
        
        .info-item {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }
        
        .info-label {
            font-size: 14px;
            color: #6b7280;
            width: 120px;
            flex-shrink: 0;
        }
        
        .info-value {
            font-size: 15px;
            color: #111;
            font-weight: 500;
        }
        
        .logout-btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background-color: #2563eb;
        }
    </style>
    <script>
        let isReload = false;

        // Handle page reload
        window.addEventListener('keydown', function(e) {
            if ((e.key === 'r' && (e.ctrlKey || e.metaKey)) || (e.key === 'F5')) {
                isReload = true;
            }
        });

        // Handle browser back button and page unload
        window.addEventListener('beforeunload', async function(e) {
            if (isReload) {
                return;
            }

            // Cancel the event
            e.preventDefault();
            // Chrome requires returnValue to be set
            e.returnValue = '';
            
            try {
                await fetch('logout.php', {
                    method: 'POST',
                    headers: {
                        'Cache-Control': 'no-cache',
                    },
                    keepalive: true
                });
            } catch (error) {
                console.error('Logout failed:', error);
            }
        });

        // Handle browser back button specifically
        window.addEventListener('popstate', function(e) {
            if (!isReload) {
                fetch('logout.php', {
                    method: 'POST',
                    headers: {
                        'Cache-Control': 'no-cache',
                    },
                    keepalive: true
                });
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <img class="profile-picture" src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
        <h1>Welcome <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
        
        <div class="user-info">
            <div class="info-item">
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Gender:</div>
                <div class="info-value"><?php echo htmlspecialchars($user['gender']); ?></div>
            </div>
        </div>
        
        <a href="logout.php" class="logout-btn">Sign Out</a>
    </div>
</body>
</html>