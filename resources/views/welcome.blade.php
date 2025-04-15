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
                        <img src="https://placehold.co/600x400/312e81/ffffff?text=CEOsofts R1" alt="About CEOsofts" class="rounded-xl shadow-lg w-full">
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

        <!-- Contact Section -->
        <section id="contact" class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-heading font-bold text-gray-900 mb-4">ติดต่อเรา</h2>
                    <p class="max-w-3xl mx-auto text-xl text-gray-600">
                        หากคุณมีคำถามหรือต้องการข้อมูลเพิ่มเติม สามารถติดต่อเราได้ตามข้อมูลด้านล่าง
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- ข้อมูลบริษัท -->
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h3 class="text-xl font-bold text-primary-700 mb-4">ข้อมูลบริษัท</h3>
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <div>
                                    <h4 class="font-semibold">บริษัท ซีอีโอซอฟท์ จำกัด</h4>
                                    <p class="text-gray-600">เลขที่ 123 อาคารเทคโนโลยี ชั้น 5</p>
                                    <p class="text-gray-600">ถนนพัฒนาการ แขวงสวนหลวง</p>
                                    <p class="text-gray-600">เขตสวนหลวง กรุงเทพฯ 10250</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <p class="text-gray-600">02-123-4567</p>
                            </div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <p class="text-gray-600">info@ceosofts.com</p>
                            </div>
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-gray-600">จันทร์-ศุกร์: 08:30 - 17:30 น.</p>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-700 mb-3">ติดตามเรา</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="text-primary-600 hover:text-primary-800">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" class="text-primary-600 hover:text-primary-800">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                                <a href="#" class="text-primary-600 hover:text-primary-800">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" class="text-primary-600 hover:text-primary-800">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10c5.51 0 10-4.48 10-10S17.51 2 12 2zm6.605 4.61a8.502 8.502 0 011.93 5.314c-.281-.054-3.101-.629-5.943-.271-.065-.141-.12-.293-.184-.445a25.416 25.416 0 00-.564-1.236c3.145-1.28 4.577-3.124 4.761-3.362zM12 3.475c2.17 0 4.154.813 5.662 2.148-.152.216-1.443 1.941-4.48 3.08-1.399-2.57-2.95-4.675-3.189-5A8.687 8.687 0 0112 3.475zm-3.633.803a53.896 53.896 0 013.167 4.935c-3.992 1.063-7.517 1.04-7.896 1.04a8.581 8.581 0 014.729-5.975zM3.453 12.01v-.26c.37.01 4.512.065 8.775-1.215.25.477.477.965.694 1.453-.109.033-.228.065-.336.098-4.404 1.42-6.747 5.303-6.942 5.629a8.522 8.522 0 01-2.19-5.705zM12 20.547a8.482 8.482 0 01-5.239-1.8c.152-.315 1.888-3.656 6.703-5.337.022-.01.033-.01.054-.022a35.318 35.318 0 011.823 6.475 8.4 8.4 0 01-3.341.684zm4.761-1.465c-.086-.52-.542-3.015-1.659-6.084 2.679-.423 5.022.271 5.314.369a8.468 8.468 0 01-3.655 5.715z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- แผนที่ หรือรูปภาพสำนักงาน -->
                    <div class="bg-white shadow-md rounded-lg p-6 lg:col-span-2">
                        <h3 class="text-xl font-bold text-primary-700 mb-4">ที่ตั้งสำนักงาน</h3>
                        <div class="aspect-w-16 aspect-h-9">
                            <!-- แทนที่ด้วย iframe แผนที่จริงหรือรูปภาพ -->
                            <div class="w-full h-80 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-gray-500 text-lg">แผนที่บริษัท (เพิ่ม Google Maps หรือรูปภาพที่นี่)</span>
                            </div>
                        </div>
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-700 mb-4">วิธีการเดินทาง</h4>
                            <ul class="space-y-2">
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    <span class="text-gray-600">รถไฟฟ้า BTS: ลงสถานีอ่อนนุช เดินต่อประมาณ 10 นาที</span>
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    <span class="text-gray-600">รถประจำทาง: สาย 23, 45, 72, 102 ลงป้ายถนนพัฒนาการ</span>
                                </li>
                                <li class="flex items-start">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    <span class="text-gray-600">รถยนต์ส่วนตัว: มีที่จอดรถฟรีสำหรับลูกค้า</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- ฟอร์มติดต่อ -->
                <div class="mt-12 bg-white shadow-md rounded-lg p-6">
                    <h3 class="text-xl font-bold text-primary-700 mb-6">ส่งข้อความถึงเรา</h3>
                    <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">ชื่อ-นามสกุล</label>
                            <input type="text" id="name" name="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">อีเมล</label>
                            <input type="email" id="email" name="email" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">เบอร์โทรศัพท์</label>
                            <input type="tel" id="phone" name="phone" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">เรื่อง</label>
                            <input type="text" id="subject" name="subject" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                        </div>
                        <div class="md:col-span-2">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">ข้อความ</label>
                            <textarea id="message" name="message" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50"></textarea>
                        </div>
                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="px-6 py-2 bg-primary-600 text-white font-medium rounded-md shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                ส่งข้อความ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>