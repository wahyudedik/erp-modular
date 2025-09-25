<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Modular - Multi-Industri Solution</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Vite Assets -->
    @vite(['resources/js/main.js'])

    <!-- Preload critical fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Meta tags for SEO -->
    <meta name="description" content="ERP Modular - Multi-Industri Solution for modern businesses">
    <meta name="keywords" content="ERP, modular, business, management, multi-industri">
    <meta name="author" content="ERP Modular Team">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Theme color -->
    <meta name="theme-color" content="#1976D2">

    <!-- Apple touch icon -->
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Manifest -->
    <link rel="manifest" href="/manifest.json">
</head>

<body>
    <div id="app">
        <!-- Loading fallback -->
        <div
            style="
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-family: 'Roboto', sans-serif;
        ">
            <div style="text-align: center;">
                <div
                    style="
                    width: 50px;
                    height: 50px;
                    border: 3px solid rgba(255,255,255,0.3);
                    border-top: 3px solid white;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 20px;
                ">
                </div>
                <h2 style="margin: 0; font-weight: 300;">Loading ERP Modular...</h2>
                <p style="margin: 10px 0 0; opacity: 0.8; font-size: 14px;">Multi-Industri Solution</p>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: #fafafa;
        }

        #app {
            min-height: 100vh;
        }
    </style>
</body>

</html>
