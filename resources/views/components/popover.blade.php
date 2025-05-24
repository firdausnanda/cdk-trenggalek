@props([
    'id' => 'popover-' . uniqid(),
    'position' => 'bottom',
    'width' => 'max-w-xs',
    'trigger' => 'click', // 'hover' or 'click'
    'triggerClass' => 'text-gray-500 dark:text-gray-400 hover:text-primary dark:hover:text-primary transition-colors',
    'contentClass' => '',
])

<div x-data="{ open: false }" class="relative inline-block">
    <!-- Trigger element that toggles the popover -->
    <button 
        @click="open = !open"
        type="button"
        class="cursor-pointer {{ $triggerClass }}"
    >
        {{ $trigger }}
    </button>

    <!-- Popover content -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.away="open = false"
        class="absolute z-50 {{ $width }} bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-0 text-sm text-gray-700 dark:text-gray-300
            {{ $position === 'top' ? 'bottom-full mb-2' : '' }}
            {{ $position === 'bottom' ? 'top-full mt-2' : '' }}
            {{ $position === 'left' ? 'right-full mr-2' : '' }}
            {{ $position === 'right' ? 'left-full ml-2' : '' }}
            {{ $contentClass }}"
        style="display: none;"
    >
        {{ $slot }}
    </div>
</div>
