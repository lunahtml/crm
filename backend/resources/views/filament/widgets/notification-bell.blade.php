<div x-data="{
    open: false,
    unreadCount: {{ auth()->user()->notifications()->whereNull('read_at')->count() ?? 0 }},
    notifications: {{ json_encode(auth()->user()->notifications()->latest()->limit(10)->get()) }}
}" class="relative">
    <button 
        @click="open = !open" 
        class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linecap="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <span 
            x-show="unreadCount > 0" 
            x-text="unreadCount"
            class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
        ></span>
    </button>

    <div 
        x-show="open" 
        @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg overflow-hidden z-50 border"
        style="display: none;"
    >
        <div class="py-2 px-3 bg-gray-50 border-b">
            <h3 class="text-sm font-semibold text-gray-700">Уведомления</h3>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            <template x-for="notification in notifications" :key="notification.id">
                <div class="px-4 py-3 border-b">
                    <p class="text-sm text-gray-800" x-text="'Новая задача: ' + (notification.data?.title || '')"></p>
                </div>
            </template>
            
            <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-gray-500">
                Нет уведомлений
            </div>
        </div>
    </div>
</div>