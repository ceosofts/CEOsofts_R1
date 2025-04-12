<div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
    <form method="GET" action="{{ route('companies.index') }}" class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
        <div class="flex-grow">
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ค้นหา</label>
            <input
                type="text"
                name="search"
                id="search"
                placeholder="ค้นหาตามชื่อหรือรหัสบริษัท"
                value="{{ request('search') }}"
                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
        </div>

        <div class="w-full md:w-48">
            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
            <select
                name="status"
                id="status"
                class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                <option value="">ทั้งหมด</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                ค้นหา
            </button>
        </div>
    </form>
</div>