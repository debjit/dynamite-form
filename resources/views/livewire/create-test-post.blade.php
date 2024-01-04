<div class="bg-gray-50 min-h-screen">
    <form wire:submit="create">
        {{ $this->form }}

        <button type="submit">
            Submit
        </button>
    </form>

    <x-filament-actions::modals />
</div>
