@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'zIndex' => 'z-50'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm sm:w-full',
    'md' => 'sm:max-w-md sm:w-full',
    'lg' => 'sm:max-w-lg sm:w-full',
    'xl' => 'sm:max-w-xl sm:w-full',
    '2xl' => 'sm:max-w-2xl sm:w-full',
    '3xl' => 'sm:max-w-3xl sm:w-full',
    '4xl' => 'sm:max-w-4xl sm:w-full',
    '5xl' => 'sm:max-w-5xl sm:w-full',
    '6xl' => 'sm:max-w-6xl sm:w-full',
    '7xl' => 'sm:max-w-7xl sm:w-full',
    'full' => 'sm:max-w-full sm:w-full',
    'fit' => 'sm:max-w-fit sm:w-auto',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 {{ $zIndex }} overflow-y-auto"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <!-- Overlay -->
    <div
        x-show="show"
        class="fixed inset-0 transition-opacity"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500/75 dark:bg-gray-900/75 opacity-75"></div>
    </div>

    <!-- Modal Content Centering Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div
            x-show="show"
            class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 {{ $maxWidth }} dark:bg-gray-800"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
            {{ $slot }}
        </div>
    </div>
</div>
