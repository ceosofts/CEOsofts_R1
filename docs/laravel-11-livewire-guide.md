# คู่มือการใช้ Livewire กับ Laravel 11

Laravel 11 มาพร้อมกับการเปลี่ยนแปลงมากมาย และการใช้งานร่วมกับ Livewire ก็มีการปรับเปลี่ยนไปด้วย คู่มือนี้จะช่วยให้คุณสามารถใช้ Livewire กับ Laravel 11 ได้อย่างถูกต้อง

## การติดตั้ง Livewire ใน Laravel 11

```bash
composer require livewire/livewire
```

## การเพิ่ม Livewire Scripts and Styles

### ใน layout หลัก (components/app-layout.blade.php)

```html
<head>
    <!-- อื่นๆ -->
    @livewireStyles
</head>
<body>
    <!-- เนื้อหาเว็บไซต์ -->
    @livewireScripts
</body>
```

## การสร้าง Livewire Component

### การสร้าง Livewire Component โดยใช้ Artisan

```bash
php artisan make:livewire Features/Counter
```

คำสั่งนี้จะสร้างไฟล์ 2 ไฟล์:

1. `app/Livewire/Features/Counter.php` - Class PHP
2. `resources/views/livewire/features/counter.blade.php` - Template Blade

> **หมายเหตุ**: หากคำสั่ง `make:livewire` ไม่ทำงาน อาจเป็นเพราะมีปัญหากับ package discovery โปรดลองรัน:
>
> ```bash
> php artisan package:discover --ansi
> ```

## การใช้ Livewire Component ใน View

### การเพิ่ม component ใน blade view:

```blade
<livewire:features.counter />
```

หรือใช้ Blade directive:

```blade
@livewire('features.counter')
```

## ตัวอย่าง Livewire Component

### การสร้าง Counter Component

#### 1. สร้าง Component:

```bash
php artisan make:livewire Components/Counter
```

#### 2. แก้ไขไฟล์ Counter.php:

```php
<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Counter extends Component
{
    public int $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.components.counter');
    }
}
```

#### 3. แก้ไขไฟล์ counter.blade.php:

```blade
<div>
    <div class="flex items-center">
        <button
            wire:click="decrement"
            class="px-4 py-2 bg-red-500 text-white rounded-l"
        >-</button>

        <span class="px-4 py-2 bg-gray-200">{{ $count }}</span>

        <button
            wire:click="increment"
            class="px-4 py-2 bg-green-500 text-white rounded-r"
        >+</button>
    </div>
</div>
```

### การสร้าง Search Component

#### 1. สร้าง Component:

```bash
php artisan make:livewire Components/Search
```

#### 2. แก้ไขไฟล์ Search.php:

```php
<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class Search extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.components.search', [
            'users' => User::where('name', 'like', '%'.$this->search.'%')
                ->orWhere('email', 'like', '%'.$this->search.'%')
                ->paginate(10),
        ]);
    }
}
```

#### 3. แก้ไขไฟล์ search.blade.php:

```blade
<div>
    <div class="mb-4">
        <input
            wire:model.live="search"
            type="text"
            placeholder="Search users..."
            class="px-4 py-2 border rounded w-full"
        />
    </div>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="px-6 py-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
```

## การใช้ Alpine.js ร่วมกับ Livewire

Alpine.js มาพร้อมกับ Livewire และสามารถใช้ร่วมกันได้อย่างมีประสิทธิภาพ:

```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle Content</button>

    <div x-show="open">
        <livewire:features.counter />
    </div>
</div>
```

## การถ่ายทอดข้อมูลระหว่าง Components

### การใช้ Events

#### 1. การส่ง Events (Emitting):

```php
// ใน Component ที่ส่ง
public function someAction()
{
    $this->dispatch('user-selected', userId: $userId);
}
```

#### 2. การรับ Events (Listening):

```php
// ใน Component ที่รับ
protected $listeners = ['user-selected' => 'handleUserSelected'];

public function handleUserSelected($userId)
{
    $this->selectedUser = User::find($userId);
}
```

## การทำ Form Validation

```php
public $name;
public $email;

protected $rules = [
    'name' => 'required|min:6',
    'email' => 'required|email',
];

public function submit()
{
    $this->validate();

    // Process form...
}
```

## การจัดการกับ Loading State

```blade
<div>
    <button wire:click="save">Save</button>

    <div wire:loading wire:target="save">
        <div class="spinner"></div> Saving...
    </div>
</div>
```

## Polling (การโหลดข้อมูลเป็นระยะ)

```blade
<div wire:poll.5s>
    Current time: {{ now() }}
</div>
```

## การทำงานกับ File Uploads

```php
use Livewire\WithFileUploads;

public $photo;

public function upload()
{
    $this->validate([
        'photo' => 'image|max:1024', // 1MB Max
    ]);

    $this->photo->store('photos');
}
```

```blade
<form wire:submit.prevent="upload">
    <input type="file" wire:model="photo">

    @error('photo') <span class="error">{{ $message }}</span> @enderror

    <button type="submit">Upload</button>
</form>
```

## คำแนะนำและเทคนิคเพิ่มเติม

1. **ลดการ Render ที่ไม่จำเป็น**: ใช้ `wire:model.lazy` หรือ `wire:model.blur` แทน `wire:model.live` เมื่อไม่ต้องการอัพเดททุกครั้งที่พิมพ์
2. **ใช้ Skeleton Loaders**: แสดง placeholder ระหว่างโหลดข้อมูล
3. **Debounce**: ลดความถี่ในการส่งคำขอโดยใช้ `wire:model.live.debounce.500ms`
4. **Batching Updates**: รวมการอัพเดทหลายครั้งเป็นหนึ่งครั้ง

## การเตรียมพร้อมสำหรับ Production

1. **Optimize Livewire Assets**: `php artisan livewire:publish --assets`
2. **ใช้ Production Mode ของ Alpine.js**: ในไฟล์ app.js
3. **ใช้ Vite ในการ Bundling**: ตั้งค่า Vite อย่างถูกต้อง

## การ Debug Livewire Components

1. **Dump and Die**: `@dump($variable)` หรือ `dd($variable)` ในไฟล์ PHP
2. **Livewire Chrome Extension**: ติดตั้ง Livewire DevTools สำหรับ Chrome
3. **Inspect Livewire Request**: ดูการสื่อสาร AJAX ใน Network tab ของ DevTools

Livewire เป็นเครื่องมือที่ทรงพลังสำหรับการสร้าง Dynamic Interface ใน Laravel โดยไม่ต้องเขียน JavaScript มากมาย การเข้าใจหลักการพื้นฐานนี้จะช่วยให้คุณสามารถสร้างแอปพลิเคชันที่มีประสิทธิภาพและตอบสนองได้ดี
