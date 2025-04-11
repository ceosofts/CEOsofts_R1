# เครื่องมือและคำแนะนำสำหรับการพัฒนา CEOsofts R1

## การเริ่มต้นพัฒนา

หลังจากติดตั้ง dependencies เรียบร้อยแล้ว คุณสามารถเริ่มต้นการพัฒนาตามขั้นตอนต่อไปนี้:

1. รันเซิร์ฟเวอร์ PHP:

    ```bash
    php artisan serve
    ```

2. รัน Vite development server (ในอีกหน้าต่าง Terminal):

    ```bash
    npm run dev
    ```

3. เข้าถึงเว็บไซต์ได้ที่:
    ```
    http://127.0.0.1:8000
    ```

## เครื่องมือช่วยพัฒนา

### สคริปต์อำนวยความสะดวก

-   **dev-start.sh**: สคริปต์สำหรับเตรียมสภาพแวดล้อมและเริ่มต้นการพัฒนา
    ```bash
    bash dev-start.sh
    ```

### การเคลียร์แคช

หากมีปัญหาเกี่ยวกับการแสดงผล หรือการเปลี่ยนแปลงไม่ปรากฏ ให้เคลียร์แคช:

```bash
php artisan optimize:clear
```

### การใช้ Terminal แบบแยกหน้าจอ

คุณสามารถใช้เครื่องมือเช่น tmux หรือ screen เพื่อแบ่งหน้าจอ Terminal:

-   **iTerm2** (สำหรับ macOS): ใช้ Split Panes (⌘+D สำหรับแบ่งแนวตั้ง, ⌘+Shift+D สำหรับแบ่งแนวนอน)

-   **tmux**:
    ```bash
    tmux new-session -s ceosofts
    # แบ่งหน้าจอแนวนอน
    Ctrl+B "
    # หรือแบ่งหน้าจอแนวตั้ง
    Ctrl+B %
    ```

## แนะนำเครื่องมือพัฒนา

### Visual Studio Code Extensions

-   **Laravel Extensions Pack**: รวมเครื่องมือสำหรับการพัฒนา Laravel
-   **Alpine.js IntelliSense**: ช่วยเติมโค้ดสำหรับ Alpine.js
-   **Tailwind CSS IntelliSense**: ช่วยเติมโค้ดสำหรับ Tailwind CSS
-   **PHP Intelephense**: Intellisense สำหรับ PHP ที่ทรงพลัง

### เครื่องมือ debugging

-   **Laravel Telescope**:

    ```bash
    composer require laravel/telescope --dev
    php artisan telescope:install
    php artisan migrate
    ```

-   **Laravel Debugbar**:
    ```bash
    composer require barryvdh/laravel-debugbar --dev
    ```

### คอมมานด์ที่มีประโยชน์

-   **แสดงเส้นทาง (routes)**:

    ```bash
    php artisan route:list
    ```

-   **Generate Livewire Component**:

    ```bash
    php artisan make:livewire Features/ComponentName
    ```

-   **Tail log**:

    ```bash
    php artisan tail --quiet
    ```

-   **Generate Factory**:
    ```bash
    php artisan make:factory ModelNameFactory
    ```

## Livewire Examples

### Form Component

```php
<?php

namespace App\Http\Livewire\Features;

use Livewire\Component;
use App\Models\YourModel;

class FormComponent extends Component
{
    public YourModel $model;

    protected $rules = [
        'model.field' => 'required|min:3',
    ];

    public function mount(YourModel $model = null)
    {
        $this->model = $model ?? new YourModel();
    }

    public function save()
    {
        $this->validate();
        $this->model->save();

        session()->flash('message', 'บันทึกข้อมูลสำเร็จ');
        return redirect()->route('your.route');
    }

    public function render()
    {
        return view('livewire.features.form-component');
    }
}
```

### Data List Component

```php
<?php

namespace App\Http\Livewire\Features;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\YourModel;

class ListComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field
            ? ($this->sortDirection === 'asc' ? 'desc' : 'asc')
            : 'asc';

        $this->sortField = $field;
    }

    public function render()
    {
        return view('livewire.features.list-component', [
            'items' => YourModel::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10)
        ]);
    }
}
```
