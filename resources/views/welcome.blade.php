<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>ERP Modular Multi-Industri</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            body {
                font-family: 'Figtree', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                background: white;
                border-radius: 20px;
                padding: 3rem;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 600px;
                margin: 2rem;
            }
            .logo {
                font-size: 3rem;
                font-weight: 700;
                color: #4f46e5;
                margin-bottom: 1rem;
            }
            .subtitle {
                font-size: 1.5rem;
                color: #6b7280;
                margin-bottom: 2rem;
            }
            .description {
                font-size: 1.1rem;
                color: #4b5563;
                line-height: 1.6;
                margin-bottom: 2rem;
            }
            .features {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1.5rem;
                margin: 2rem 0;
            }
            .feature {
                padding: 1.5rem;
                background: #f8fafc;
                border-radius: 12px;
                border: 2px solid #e2e8f0;
            }
            .feature-icon {
                font-size: 2rem;
                margin-bottom: 0.5rem;
            }
            .feature-title {
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.5rem;
            }
            .feature-desc {
                color: #6b7280;
                font-size: 0.9rem;
            }
            .api-info {
                background: #f0f9ff;
                border: 1px solid #0ea5e9;
                border-radius: 8px;
                padding: 1.5rem;
                margin: 2rem 0;
            }
            .api-title {
                color: #0369a1;
                font-weight: 600;
                margin-bottom: 1rem;
            }
            .api-endpoint {
                background: #1e293b;
                color: #e2e8f0;
                padding: 0.5rem 1rem;
                border-radius: 6px;
                font-family: monospace;
                margin: 0.5rem 0;
                display: inline-block;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">🏭 ERP Modular</div>
            <div class="subtitle">Multi-Industri Solution</div>
            
            <div class="description">
                Sistem ERP yang modular dan scalable untuk berbagai jenis usaha dengan konsep 
                <strong>Register → Pilih Jenis Usaha → Aktivasi Modul Rekomendasi</strong>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🏗️</div>
                    <div class="feature-title">Modular Architecture</div>
                    <div class="feature-desc">Setiap modul dapat diaktifkan/dinonaktifkan sesuai kebutuhan</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎯</div>
                    <div class="feature-title">Smart Recommendations</div>
                    <div class="feature-desc">Sistem merekomendasikan modul berdasarkan jenis usaha</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">📈</div>
                    <div class="feature-title">Scalable Design</div>
                    <div class="feature-desc">Mudah menambah jenis usaha dan modul baru</div>
                </div>
            </div>

            <div class="api-info">
                <div class="api-title">🚀 API Endpoints</div>
                <div class="api-endpoint">GET /api/v1/business-types</div>
                <div class="api-endpoint">GET /api/v1/modules</div>
                <div class="api-endpoint">GET /api/v1/modules/categories/list</div>
            </div>

            <div style="color: #6b7280; font-size: 0.9rem; margin-top: 2rem;">
                Laravel {{ app()->version() }} | PHP {{ PHP_VERSION }}
            </div>
        </div>
    </body>
</html>
