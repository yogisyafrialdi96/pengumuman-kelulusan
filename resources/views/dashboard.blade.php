<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl p-4">
        <flux:heading level="1" class="!text-2xl">Dashboard</flux:heading>
        @livewire('admin.dashboard-stats')
    </div>
</x-layouts::app>

