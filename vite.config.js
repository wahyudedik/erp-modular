import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { resolve } from 'path'

export default defineConfig({
    plugins: [
        vue(),
        vuetify({
            autoImport: true,
        }),
        AutoImport({
            imports: [
                'vue',
                'vue-router',
                'pinia',
                '@vueuse/core'
            ],
            dts: true,
            eslintrc: {
                enabled: true
            }
        }),
        Components({
            dts: true
        })
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
            '@components': resolve(__dirname, 'resources/js/components'),
            '@views': resolve(__dirname, 'resources/js/views'),
            '@stores': resolve(__dirname, 'resources/js/stores'),
            '@api': resolve(__dirname, 'resources/js/api'),
            '@utils': resolve(__dirname, 'resources/js/utils'),
            '@assets': resolve(__dirname, 'resources/assets')
        }
    },
    server: {
        host: '0.0.0.0',
        port: 3000,
        hmr: {
            host: 'localhost'
        },
        proxy: {
            '/api': {
                target: 'http://localhost:8001',
                changeOrigin: true,
                secure: false,
            }
        }
    },
    build: {
        outDir: 'public/dist',
        assetsDir: 'assets',
        sourcemap: false,
        rollupOptions: {
            input: {
                app: './resources/js/main.js'
            },
            output: {
                manualChunks: {
                    vendor: ['vue', 'vue-router', 'pinia'],
                    charts: ['chart.js', 'vue-chartjs'],
                    ui: ['vuetify']
                }
            }
        }
    }
})
