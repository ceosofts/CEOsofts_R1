/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    // จำกัดการสแกนเฉพาะไฟล์ที่จำเป็น
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
    // ไม่รวม tests, documentation และไฟล์ที่ไม่จำเป็น
    // "./resources/**/*.vue", // ตัดออกเพราะดูเหมือนไม่ได้ใช้ Vue
    // "./resources/**/*.css", // CSS จะถูกสร้างจาก Tailwind อยู่แล้ว ไม่จำเป็นต้องสแกน
    "./app/View/**/*.php",     // เฉพาะ View components แทนการสแกน app ทั้งหมด
    // "./config/**/*.php",    // ตัดออกเพราะมีโอกาสน้อยที่ config จะมี CSS classes
  ],
  
  // เพิ่ม safelist สำหรับคลาสที่ใช้บ่อยแต่อาจไม่ถูกตรวจพบในไฟล์
  safelist: [
    'bg-primary-500',
    'text-primary-500',
    'bg-secondary-500',
    'text-secondary-500',
  ],

  theme: {
    extend: {
      colors: {
        primary: {
          // ลดจำนวนเฉดสี ใช้เฉพาะที่จำเป็น
          50: '#eef2ff',
          100: '#e0e7ff',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          900: '#312e81',
        },
        secondary: {
          50: '#f0fdfa',
          100: '#ccfbf1',
          500: '#14b8a6',
          600: '#0d9488',
          700: '#0f766e',
          900: '#134e4a',
        },
        accent: {
          100: '#ffefc2',
          500: '#fe9c03',
          600: '#ef7c00',
          900: '#7e3c0f',
        }
      },
      fontFamily: {
        sans: ['Sarabun', 'sans-serif'],
        heading: ['Prompt', 'sans-serif'],
      },
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
        '128': '32rem',
        '144': '36rem',
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '2rem',
        '3xl': '3rem',
        '4xl': '4rem',
      },
      screens: {
        'xs': '475px',
        '3xl': '1920px',
      },
      animation: {
        'spin-slow': 'spin 3s linear infinite',
        'bounce-slow': 'bounce 3s infinite',
        'fade-in': 'fadeIn 0.5s ease-in-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
      },
    },
  },
  
  // ลดจำนวน plugins ลงและเพิ่มการตั้งค่าประสิทธิภาพ
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
  
  // เพิ่มการตั้งค่าเพื่อปรับปรุงประสิทธิภาพ
  future: {
    hoverOnlyWhenSupported: true, // ลดจำนวน CSS ที่สร้าง
  },
  
  // ลด variants เพื่อลดขนาด CSS
  variants: {
    extend: {
      // เก็บเฉพาะ variants ที่จำเป็น
      opacity: ['hover', 'focus'],
      backgroundColor: ['hover', 'focus'],
      textColor: ['hover', 'focus'],
    }
  }
}
