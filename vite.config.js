import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],

    // ปรับปรุงการตั้งค่าเซิร์ฟเวอร์เพื่อลดการใช้ทรัพยากร
    server: {
        // ปิดการทำงานของ HMR เพื่อลด CPU usage
        hmr: false,
        
        // ตั้งค่าเพื่อลดการติดตามไฟล์
        watch: {
            usePolling: false,
            // ไม่ติดตามไฟล์เหล่านี้
            ignored: ['**/node_modules/**', '**/vendor/**', '**/storage/**', '**/.git/**'],
        },
    },
    
    // แก้ปัญหา fsevents และตั้งค่า build
    build: {
        // แก้ไขปัญหา fsevents module
        rollupOptions: {
            external: [
                'fsevents',
                // เพิ่ม modules อื่นๆ ที่อาจมีปัญหาใน macOS
                'chokidar',
            ],
            output: {
                manualChunks: {
                    vendor: ['laravel-vite-plugin']
                }
            }
        },
        
        // เพิ่มการใช้งาน cache
        commonjsOptions: {
            include: [/node_modules/],
        },
        
        // ลดขนาดไฟล์ที่สร้าง
        cssCodeSplit: true,
        
        // เพิ่มการบีบอัดไฟล์
        chunkSizeWarningLimit: 1000,
    },
    
    // เพิ่มประสิทธิภาพของ dependencies
    optimizeDeps: {
        // เพิ่มการ cache
        cacheDir: 'node_modules/.vite',
        
        // ไม่ต้องแปลง dependencies ที่ไม่จำเป็น
        exclude: ['laravel-vite-plugin'],
    },
    
    // กำหนดการแก้ไข path
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
        },
    }
});
