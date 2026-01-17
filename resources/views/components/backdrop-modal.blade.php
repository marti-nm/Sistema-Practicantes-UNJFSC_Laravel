@props([
    'name'
])

<div x-show="{{ $name }}" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    class="absolute inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm"
    @click="{{ $name }} = false">
</div>