<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading text-3xl text-white bg-primary-700 p-6 rounded-lg shadow text-center">
            ยินดีต้อนรับสู่ CEOsofts R1
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- Hero Section -->
        <section class="relative bg-gradient-to-br from-primary-700 to-primary-900 text-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                    <div class="text-center md:text-left">
                        <!-- เพิ่มโลโก้ขนาดใหญ่ที่นี่ -->
                        <div class="flex justify-center md:justify-start mb-6">
                            <img src="{{ asset('img/ceo_logo9.ico') }}" alt="CEOsofts Logo" class="h-16 w-auto">
                        </div>
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-heading font-bold leading-tight mb-6">
                            พัฒนาธุรกิจของคุณด้วย<span class="text-accent-400"> โซลูชันที่ทันสมัย</span>
                        </h1>
                        <p class="text-lg md:text-xl mb-8 text-gray-100">
                            เราเชี่ยวชาญในการพัฒนาซอฟต์แวร์เพื่อธุรกิจ ช่วยให้คุณก้าวล้ำนำหน้าคู่แข่ง
                            ด้วยเทคโนโลยีล่าสุดและทีมงานผู้เชี่ยวชาญ
                        </p>
                        <div class="flex flex-col sm:flex-row justify-center md:justify-start gap-4">
                            <x-button type="accent" class="text-lg px-8 py-3">
                                เริ่มต้นเลย
                            </x-button>
                            <x-button type="outline" class="text-lg px-8 py-3 text-white border-white hover:bg-white hover:text-primary-700">
                                ดูบริการ
                            </x-button>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <img src="https://placehold.co/600x400/2dd4bf/ffffff?text=CEOsofts+R1" alt="CEOsofts R1" class="w-full rounded-xl shadow-lg animate-fade-in">
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden opacity-10">
                <div class="absolute top-0 -left-10 w-72 h-72 bg-accent-500 rounded-full mix-blend-multiply filter blur-xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-secondary-500 rounded-full mix-blend-multiply filter blur-xl"></div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-heading font-bold text-gray-900 mb-4">บริการของเรา</h2>
                    <p class="max-w-3xl mx-auto text-xl text-gray-600">
                        เรามุ่งมั่นพัฒนาโซลูชันที่ตอบโจทย์ธุรกิจของคุณ
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <x-card class="hover:shadow-lg transition-shadow duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-heading font-bold mb-3">พัฒนาเว็บไซต์</h3>
                            <p class="text-gray-600">
                                เราออกแบบและพัฒนาเว็บไซต์ที่ตอบสนองความต้องการของธุรกิจคุณ ด้วยเทคโนโลยีล่าสุด
                            </p>
                        </div>
                    </x-card>

                    <!-- Feature 2 -->
                    <x-card class="hover:shadow-lg transition-shadow duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-secondary-100 text-secondary-700 rounded-full flex items-center justify-center mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-heading font-bold mb-3">พัฒนาแอปพลิเคชัน</h3>
                            <p class="text-gray-600">
                                สร้างแอปพลิเคชันมือถือที่เชื่อมโยงกับธุรกิจของคุณ ทั้งระบบ iOS และ Android
                            </p>
                        </div>
                    </x-card>

                    <!-- Feature 3 -->
                    <x-card class="hover:shadow-lg transition-shadow duration-300">
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-accent-100 text-accent-700 rounded-full flex items-center justify-center mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-heading font-bold mb-3">ที่ปรึกษาทางธุรกิจ</h3>
                            <p class="text-gray-600">
                                วางแผนและให้คำปรึกษาด้านเทคโนโลยีเพื่อเพิ่มประสิทธิภาพในการดำเนินธุรกิจของคุณ
                            </p>
                        </div>
                    </x-card>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div>
                        <img src="https://placehold.co/600x400/312e81/ffffff?text=เกี่ยวกับเรา" alt="About CEOsofts" class="rounded-xl shadow-lg w-full">
                    </div>
                    <div>
                        <h2 class="text-3xl md:text-4xl font-heading font-bold text-gray-900 mb-6">เกี่ยวกับเรา</h2>
                        <p class="text-lg text-gray-600 mb-6">
                            CEOsofts ก่อตั้งขึ้นในปี 2023 โดยมีเป้าหมายในการช่วยเหลือธุรกิจต่างๆ ในการปรับตัวเข้าสู่ยุคดิจิทัล
                            เรามีทีมงานผู้เชี่ยวชาญที่มีประสบการณ์มากกว่า 10 ปีในด้านการพัฒนาซอฟต์แวร์
                        </p>
                        <p class="text-lg text-gray-600 mb-6">
                            เราเชื่อในการสร้างความสัมพันธ์ระยะยาวกับลูกค้า และมุ่งมั่นในการส่งมอบผลงานที่มีคุณภาพสูง
                            ตรงตามความต้องการและเป้าหมายของธุรกิจคุณ
                        </p>
                        <x-button type="primary" href="/about" class="text-lg px-6 py-2">
                            อ่านเพิ่มเติม
                        </x-button>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-16 bg-gradient-to-br from-secondary-700 to-secondary-900 text-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl md:text-4xl font-heading font-bold mb-6">
                    พร้อมที่จะยกระดับธุรกิจของคุณแล้วหรือยัง?
                </h2>
                <p class="text-xl mb-8 max-w-3xl mx-auto">
                    เราพร้อมช่วยให้ธุรกิจของคุณเติบโตด้วยเทคโนโลยีที่ทันสมัย ติดต่อเราเพื่อรับคำปรึกษาฟรี
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <x-button type="accent" class="text-lg px-8 py-3">
                        ติดต่อเรา
                    </x-button>
                    <x-button type="outline" class="text-lg px-8 py-3 text-white border-white hover:bg-white hover:text-secondary-700">
                        ดูผลงาน
                    </x-button>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
