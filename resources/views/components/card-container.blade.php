<div class="bg-white dark:bg-slate-900 border border-slate-200 rounded-2xl shadow-sm p-4">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold">{{ $title }}</h3>

        <div>
            {{ $action ?? '' }}
        </div>
    </div>

    <div class="mt-2">
        {{ $slot }}
    </div>
</div>
