<?php

use Livewire\Volt\Component;
use App\Models\DevelopmentArea;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;


new class extends Component {
    use WithPagination;
    //
    public $developmentAreas = null;
    public $showModal = false;
    public $currentArea = null;
    public string $name = '';
    public $isEditing = false;
    public $showDeleteConfirmation = false;
    public $areaId = null;
    public $pageLimit = 10;
    public $totalRecords = 0;
    public array $pageLimitOptions = [];


    public function mount()
    {
        $this->developmentAreas = DevelopmentArea::all();
        $this->totalRecords = DevelopmentArea::count();
        $this->pageLimitOptions =  [
        ['label'=>10,'value'=>10],
        ['label'=>25,'value'=>25],
        ['label'=>50,'value'=>50],
        ['label'=>100,'value'=>100],
        ['label'=>250,'value'=>250],
        ['label'=>500,'value'=>500],
        ['label'=>number_format(1000),'value'=>1000],
        ['label'=>'View All ('.number_format($this->totalRecords).')','value'=>$this->totalRecords],];
    }

    public function loadAreas()
    {
        return DevelopmentArea::query()->paginate($this->pageLimit);
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentArea = null;
    }
    public function openEditModal($areaId)
    {
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentArea = DevelopmentArea::find($areaId);
        $this->name = $this->currentArea->name;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['name']);
        $this->currentArea = null;
        $this->isEditing = false;
    }
    public function addArea()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        DevelopmentArea::create($validated);
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Development Area added successfully'
        );
        $this->reset(['name']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->closeModal();
        $this->developmentAreas = DevelopmentArea::all();
    }
    public function editArea()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
        ]);
        $this->currentArea->update($validated);
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Development Area updated successfully'
        );
        $this->reset(['name']);
        $this->showModal = false;
        $this->isEditing = false;
        $this->closeModal();
        $this->developmentAreas = DevelopmentArea::all();
    }
    public function confirmDelete($areaId)
    {
        $this->areaId = $areaId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteArea()
    {
        $area = DevelopmentArea::find($this->areaId);
        if ($area) {
            $area->delete();
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Development Area deleted successfully'
            );
            $this->developmentAreas = DevelopmentArea::all();
        }
        $this->showDeleteConfirmation = false;
        $this->areaId = null;
    }
    
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Development Areas" class="mx-auto">
        <x-slot name="action">
            <x-button primary wire:click="openModal" icon="plus" label="Add Development Area" />
        </x-slot>
        <div class="mb-3 flex justify-start items-center gap-2">
             <div class="w-50">
                <x-select
                    wire:model.live='pageLimit'
                    :options='$pageLimitOptions'
                    option-label='label'
                    option-value='value'
                    label="Page Limit"
                    :clearable="false"
                />
             </div>
           </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($this->loadAreas() as $area)
                    <tr>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $area->name }}</td>
                        <td class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <x-button wire:click="openEditModal({{ $area->id }})" icon="pencil" label="Edit" />
                            <x-button wire:click="confirmDelete({{ $area->id }})" icon="trash" label="Delete" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $this->loadAreas()->links() }}
        @if($showModal)
            <x-modal wire:model.defer="showModal">
                <x-card>
                    <form wire:submit.prevent="{{ $isEditing ? 'editArea' : 'addArea' }}">
                    <x-input label="Name" wire:model.defer="name" required placeholder="Enter area name" />
                    <div class="mt-4 flex justify-end space-x-2">
                        <x-button primary type="submit">{{ $isEditing ? 'Update Area' : 'Add Area' }}</x-button>
                        <x-button secondary wire:click="closeModal">Cancel</x-button>
                    </div>
                </form>
                </x-card>
            </x-modal>
        @endif

        @if($showDeleteConfirmation)
            <x-modal
                wire:model.defer="showDeleteConfirmation"
                
            >
            <x-card title="Delete Development Area">
                <p>Are you sure you want to delete this development area?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <x-button primary wire:click="deleteArea">Delete</x-button>
                    <x-button secondary wire:click="$set('showDeleteConfirmation', false)">Cancel</x-button>
                </div>
            </x-card>
        </x-modal>
        @endif
    </x-card>
</div>
