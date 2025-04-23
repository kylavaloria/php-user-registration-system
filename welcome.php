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
    <script>
        // Handle browser back button and page unload
        window.addEventListener('beforeunload', async function(e) {
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
            fetch('logout.php', {
                method: 'POST',
                headers: {
                    'Cache-Control': 'no-cache',
                },
                keepalive: true
            });
        });
    </script>
</head>
<body>
    <img src="uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" 
         alt="Profile Picture" 
         width="150">
    <h1>Welcome <?php echo htmlspecialchars($user['fullname']); ?>!</h1>
    <p>Your email address is: <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Gender: <?php echo htmlspecialchars($user['gender']); ?></p>
</body>
</html>