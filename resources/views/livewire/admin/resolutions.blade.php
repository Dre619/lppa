<?php

use Livewire\Volt\Component;
use App\Models\Resolution;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;
    // Properties
    public $resolution_type = null;
    public $description = '';
    //follow this resolution model
    /*
        [
		'resolution_type',
		'description'
	];
    */
    public $resolutions = null;
    public bool $showModal = false;
    public $currentResolution = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $resolutionId = null;
    public $pageLimit = 10;
    public $totalRecords = 0;
    public array $pageLimitOptions = [];

    public function mount()
    {
        //$this->resolutions = Resolution::all();
        $this->totalRecords = Resolution::count();
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

    public function loadData()
    {
        return Resolution::query()->orderBy('id','desc')->paginate($this->pageLimit);
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['resolution_type', 'description']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentResolution = null;
    }
    public function openEditModal($resolutionId)
    {
        $this->resetErrorBag();
        $this->reset(['resolution_type', 'description']);
        $this->currentResolution = Resolution::find($resolutionId);
        $this->resolution_type = $this->currentResolution->resolution_type;
        $this->description = $this->currentResolution->description;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['resolution_type', 'description']);
        $this->currentResolution = null;
        $this->isEditing = false;
    }
    public function addResolution()
    {
        $validated = $this->validate([
            'resolution_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        Resolution::create($validated);
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Resolution added successfully'
        );
        $this->reset(['resolution_type', 'description']);
        $this->resolutions = Resolution::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function editResolution()
    {
        $validated = $this->validate([
            'resolution_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        $this->currentResolution->update($validated);
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Resolution updated successfully'
        );
        $this->reset(['resolution_type', 'description']);
        $this->resolutions = Resolution::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function confirmDelete($resolutionId)
    {
        $this->resolutionId = $resolutionId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteResolution()
    {
        if ($this->resolutionId) {
            Resolution::destroy($this->resolutionId);
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Resolution deleted successfully'
            );
            $this->resolutions = Resolution::all();
            $this->showDeleteConfirmation = false;
        } else {
            $this->dispatch('toastMagic',
                status: 'error',
                title: 'Error',
                message: 'Resolution not found'
            );
        }
        $this->resolutionId = null;
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Resolutions" class="mx-auto">
        <x-slot name="action">
            <x-button primary wire:click="openModal" icon="plus" label="Add Resolution" />
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Resolution Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($this->loadData() as $resolution)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $resolution->resolution_type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $resolution->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-button secondary wire:click="openEditModal({{ $resolution->id }})" icon="pencil" label="Edit" />
                            <x-button danger wire:click="confirmDelete({{ $resolution->id }})" icon="trash" label="Delete" />
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $this->loadData()->links() }}
    </x-card>

    @if($showModal)
        <x-modal wire:model.defer="showModal">
            <x-card title="{{ $isEditing ? 'Edit Resolution' : 'Add Resolution' }}">
                <form wire:submit.prevent="{{ $isEditing ? 'editResolution' : 'addResolution' }}">
                    <div class="space-y-4">
                        <div>
                            <x-input type="text" id="resolution_type" wire:model.defer="resolution_type"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   required label="Resolution Type"/>
                            <x-textarea id="description" wire:model.defer="description"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   label="Description" placeholder="Enter description here" />
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <x-button primary type="submit">{{ $isEditing ? 'Update Resolution' : 'Add Resolution' }}</x-button>
                            <x-button secondary wire:click="closeModal">Cancel</x-button>
                        </div> 
                    </div>
                </form>
            </x-card>
        </x-modal>
    @endif
        @if($showDeleteConfirmation)
            <x-modal
                wire:model.defer="showDeleteConfirmation"
            >
            <x-card>
                <p>Are you sure you want to delete this resolution?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <x-button primary wire:click="deleteResolution">Delete</x-button>
                    <x-button secondary wire:click="$set('showDeleteConfirmation', false)">Cancel</x-button>
                </div>
            </x-card>
        </x-modal>
        @endif
</div>
