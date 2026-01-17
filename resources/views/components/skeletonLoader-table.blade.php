<!-- Skeleton Loader - Simple gray silhouettes -->
<div id="skeletonLoader" class="skeleton-loader">
    <!-- Top Controls (Length + Search) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-8 px-2 mb-4">
        <div class="skeleton-box h-10 w-32"></div>
        <div class="skeleton-box h-10 w-72"></div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto bg-gray-100 dark:bg-slate-900/50 rounded-2xl">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900 px-6 py-4 rounded-t-2xl">
            <div class="flex gap-4">
                <div class="skeleton-box h-4 w-16 bg-white/20"></div>
                <div class="skeleton-box h-4 w-32 bg-white/20"></div>
                <div class="skeleton-box h-4 flex-1 bg-white/20"></div>
                <div class="skeleton-box h-4 w-24 bg-white/20"></div>
            </div>
        </div>
        
        <!-- Table Rows -->
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @for($i = 0; $i < 2; $i++)
            <div class="px-6 py-5 flex gap-4 items-center">
                <div class="skeleton-box h-4 w-12"></div>
                <div class="flex items-center gap-3 flex-1">
                    <div class="skeleton-box w-10 h-10 rounded-xl"></div>
                    <div class="skeleton-box h-4 w-40"></div>
                </div>
                <div class="skeleton-box h-4 flex-1"></div>
                <div class="flex gap-2">
                    <div class="skeleton-box h-9 w-9 rounded-xl"></div>
                    <div class="skeleton-box h-9 w-9 rounded-xl"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
    
    <!-- Bottom Controls (Info + Pagination) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-8 pb-2 px-2 mt-4">
        <div class="skeleton-box h-4 w-48"></div>
        <div class="flex gap-2">
            <div class="skeleton-box h-9 w-20 rounded-xl"></div>
            <div class="skeleton-box h-9 w-9 rounded-xl"></div>
            <div class="skeleton-box h-9 w-9 rounded-xl"></div>
            <div class="skeleton-box h-9 w-20 rounded-xl"></div>
        </div>
    </div>
</div>