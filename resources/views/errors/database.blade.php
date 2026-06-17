<!DOCTYPE html>
<html>
<head>
    <title>Service Unavailable</title>
</head>
<body>
    <div style="text-align:center;margin-top:100px;">
        <h1>503</h1>
        <h2>Database Connection Error</h2>
        <p>{{ $message }}</p>
        <button onclick="window.location.reload()">
            Retry
        </button>
    </div>
</body>
</html>