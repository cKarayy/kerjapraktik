<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link rel="stylesheet" href="{{ asset('css/styleLanding.css') }}">
</head> 
<body>
    <div class="container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
        <a href="{{ url('login') }}" class="login-btn">LOGIN</a>
    </div>
</body>
</html>