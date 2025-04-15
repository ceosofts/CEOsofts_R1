<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                แผนผังองค์กร: {{ $company->name }}
            </h2>
            <div>
                <a href="{{ route('organization.structure.index') }}" class="text-sm bg-gray-500 hover:bg-gray-700 text-white py-1 px-3 rounded">
                    กลับ
                </a>
                <a href="{{ route('organization.structure.edit', $company->id) }}" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded ml-2">
                    จัดการโครงสร้าง
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Organization Chart Container -->
                    <div class="org-chart-container" id="orgChart" style="height: 600px; width: 100%">
                        <div class="flex justify-center items-center h-full">
                            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-500 text-lg">กำลังโหลดแผนผังองค์กร...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Debug Section เพื่อช่วยตรวจสอบปัญหา -->
        @if(config('app.debug'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
                <div class="bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ showDebug: false }">
                    <button 
                        @click="showDebug = !showDebug" 
                        class="mb-4 px-3 py-1 text-xs font-medium rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                        <span x-text="showDebug ? 'ซ่อนข้อมูล Debug' : 'แสดงข้อมูล Debug'"></span>
                    </button>
                    
                    <div x-show="showDebug" x-cloak>
                        <h3 class="font-semibold text-lg mb-2 text-gray-700">Debug Information:</h3>
                        <div class="mb-4">
                            <p><strong>API Endpoint:</strong> <code id="apiEndpoint">/api/organization/{{ $company->id }}/data</code></p>
                            <p><strong>Company ID:</strong> {{ $company->id }}</p>
                            <p><strong>Department Count:</strong> {{ $company->departments->count() }}</p>
                            <button id="testApiBtn" class="mt-2 px-3 py-1 text-xs font-medium rounded bg-blue-500 hover:bg-blue-600 text-white">Test API Call</button>
                        </div>
                        <pre id="apiResponse" class="bg-gray-800 text-green-400 p-4 rounded-md overflow-auto text-xs mt-2">API response will appear here...</pre>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- เพิ่ม OrgChart JS library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/7.8.5/d3.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เพิ่มการจัดการกับ error ที่ดีขึ้น
            fetchOrganizationData();
            
            // เพิ่มการทดสอบ API สำหรับ debug
            if (document.getElementById('testApiBtn')) {
                document.getElementById('testApiBtn').addEventListener('click', function() {
                    fetchOrganizationData(true);
                });
            }
        });
        
        function fetchOrganizationData(isDebug = false) {
            const endpoint = `/api/organization/${@json($company->id)}/data`;
            const responseElement = document.getElementById('apiResponse');
            
            fetch(endpoint)
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errorData => {
                            throw {
                                status: response.status,
                                statusText: response.statusText,
                                errorData: errorData
                            };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (isDebug && responseElement) {
                        responseElement.textContent = JSON.stringify(data, null, 2);
                    }
                    renderOrganizationChart(data.structure);
                })
                .catch(error => {
                    console.error('Error loading organization data:', error);
                    
                    let errorMessage = 'เกิดข้อผิดพลาดในการโหลดข้อมูล';
                    let errorDetails = '';
                    
                    if (error.errorData && error.errorData.message) {
                        errorDetails = error.errorData.message;
                    } else if (error.message) {
                        errorDetails = error.message;
                    }
                    
                    document.getElementById('orgChart').innerHTML = `
                        <div class="flex justify-center items-center h-full">
                            <div class="text-red-500 text-center">
                                <p class="text-lg font-semibold">${errorMessage}</p>
                                <p class="text-sm mt-2">กรุณาลองใหม่อีกครั้ง</p>
                                <p class="text-xs mt-4 text-gray-500">${errorDetails}</p>
                            </div>
                        </div>
                    `;
                    
                    if (isDebug && responseElement) {
                        responseElement.textContent = `Error: ${JSON.stringify(error, null, 2)}`;
                    }
                });
        }

        function renderOrganizationChart(data) {
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if (!data) {
                throw new Error('No data provided for chart rendering');
            }
            
            const container = document.getElementById('orgChart');
            container.innerHTML = '';
            
            // ฟังก์ชันสร้างแผนผังองค์กรด้วย D3.js
            const width = container.offsetWidth;
            const height = container.offsetHeight;
            
            const margin = { top: 50, right: 50, bottom: 50, left: 50 };
            const innerWidth = width - margin.left - margin.right;
            const innerHeight = height - margin.top - margin.bottom;
            
            const svg = d3.select('#orgChart')
                .append('svg')
                .attr('width', width)
                .attr('height', height);
                
            const g = svg.append('g')
                .attr('transform', `translate(${margin.left}, ${margin.top})`);
                
            // สร้าง hierarchy จากข้อมูล
            const root = d3.hierarchy(data);
            
            // ตรวจสอบว่ามี node หรือไม่
            if (!root || !root.descendants || root.descendants().length === 0) {
                container.innerHTML = `
                    <div class="flex justify-center items-center h-full">
                        <div class="text-yellow-500 text-center">
                            <p class="text-lg font-semibold">ไม่พบข้อมูลโครงสร้างองค์กร</p>
                            <p class="text-sm mt-2">กรุณาตั้งค่าแผนกและตำแหน่งก่อน</p>
                        </div>
                    </div>
                `;
                return;
            }
            
            // กำหนดขนาดของต้นไม้
            const treeLayout = d3.tree().size([innerWidth, innerHeight]);
            
            // คำนวณตำแหน่งของแต่ละ node
            const links = treeLayout(root).links();
            
            // วาดเส้นเชื่อม
            g.selectAll('.link')
                .data(links)
                .enter().append('path')
                .attr('class', 'link')
                .attr('fill', 'none')
                .attr('stroke', '#ccc')
                .attr('stroke-width', 1.5)
                .attr('d', d3.linkVertical()
                    .x(d => d.x)
                    .y(d => d.y));
                
            // สร้าง node
            const node = g.selectAll('.node')
                .data(root.descendants())
                .enter().append('g')
                .attr('class', d => `node ${d.children ? " node--internal" : " node--leaf"}`)
                .attr('transform', d => `translate(${d.x}, ${d.y})`);
                
            // เพิ่มกรอบ node
            node.append('rect')
                .attr('width', 120)
                .attr('height', 60)
                .attr('x', -60)
                .attr('y', -30)
                .attr('rx', 5)
                .attr('ry', 5)
                .attr('fill', d => {
                    if (d.data.id.startsWith('company')) return '#1e40af';
                    if (d.data.id.startsWith('dept')) return '#047857';
                    if (d.data.id.startsWith('pos')) return '#b45309';
                    return '#7e22ce';
                })
                .attr('stroke', '#fff')
                .attr('stroke-width', 2);
                
            // เพิ่มชื่อตำแหน่ง
            node.append('text')
                .attr('dy', '-10')
                .attr('text-anchor', 'middle')
                .attr('fill', '#fff')
                .attr('font-size', '10px')
                .text(d => d.data.title);
                
            // เพิ่มชื่อ
            node.append('text')
                .attr('dy', '10')
                .attr('text-anchor', 'middle')
                .attr('fill', '#fff')
                .attr('font-size', '12px')
                .attr('font-weight', 'bold')
                .text(d => {
                    const name = d.data.name;
                    return name.length > 15 ? name.substring(0, 15) + '...' : name;
                });
                
            // เพิ่มฟังก์ชัน zoom และ pan
            const zoom = d3.zoom()
                .scaleExtent([0.5, 2])
                .on('zoom', (event) => {
                    g.attr('transform', event.transform);
                });
                
            svg.call(zoom);
        }
    </script>

    <style>
        .node rect {
            cursor: pointer;
        }
        .node text {
            pointer-events: none;
        }
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
