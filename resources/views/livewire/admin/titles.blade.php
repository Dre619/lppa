<?php

use Livewire\Volt\Component;
use App\Models\ApplicantTitle;

new class extends Component {
    public $title = '';
    public $titles = null;
    public bool $showModal = false;
    public $currentTitle = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $titleId = null;

    public function mount()
    {
        $this->titles = ApplicantTitle::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['title']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentTitle = null;
    }
    public function openEditModal($titleId)
    {
        $this->resetErrorBag();
        $this->reset(['title']);
        $this->currentTitle = ApplicantTitle::find($titleId);
        $this->title = $this->currentTitle->title;
        $this->showModal = true;
        $this->isEditing = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['title']);
        $this->currentTitle = null;
        $this->isEditing = false;
    }
    public function addTitle()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
        ]);
        ApplicantTitle::create($validated);
        $this->dispatch('toastMagic',
            status: 'success',title:'Success',
            message: 'Title added successfully'
        );
        $this->reset(['title']);
        $this->titles = ApplicantTitle::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updateTitle()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
        ]);
        $this->currentTitle->update($validated);
        $this->dispatch('toastMagic',
            status: 'success',title:'Success',
            message: 'Title updated successfully'
        );
        $this->reset(['title']);
        $this->titles = ApplicantTitle::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function confirmDelete($titleId)
    {
        $this->titleId = $titleId;
        $this->showDeleteConfirmation = true;
    }
    public function deleteTitle()
    {
        $title = ApplicantTitle::find($this->titleId);
        if ($title) {
            $title->delete();
            $this->dispatch('toastMagic',
                status: 'success',title:'Success',
                message: 'Title deleted successfully'
            );
            $this->titles = ApplicantTitle::all();
        } else {
            $this->dispatch('toastMagic',
                status: 'error',
                title: 'Error',
                message: 'Title not found'
            );
        }
        $this->showDeleteConfirmation = false;
        $this->titleId = null;
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Titles" class="mx-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Title</x-button>
        </x-slot>

        <table class="min-w-full ">
            <thead>
                <tr>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 border-b-2 border-gray-200 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($titles as $title)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">{{ $title->title }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <x-button wire:click="openEditModal({{ $title->id }})" primary label="Edit" icon="pencil"/>
                            <x-button wire:click="confirmDelete({{ $title->id }})" danger label="Delete" icon="trash"/>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($showModal)
           <x-modal wire:model="showModal">
                <x-card title="{{ $isEditing ? 'Edit Title' : 'Add Title' }}">
                    <form wire:submit.prevent="{{ $isEditing ? 'updateTitle' : 'addTitle' }}">
                        <div class="mb-4">
                            <x-input
                                label="Title"
                                wire:model.defer="title"
                                required
                                placeholder="Enter Title"
                            />
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <x-button primary type="submit">{{ $isEditing ? 'Update Title' : 'Add Title' }}</x-button>
                            <x-button wire:click="closeModal" secondary>Cancel</x-button>
                        </div>
                    </form>
                </x-card>
           </x-modal>
        @endif

        @if($showDeleteConfirmation)
            <x-modal wire:model="showDeleteConfirmation">
                <x-card title="Confirm Deletion">
                    <p>Are you sure you want to delete this Title?</p>
                    <div class="mt-4">
                        <x-button wire:click="deleteTitle" danger label="Delete" />
                        <x-button wire:click="closeDeleteConfirmation" secondary label="Cancel" />
                    </div>
                </x-card>
            </x-modal>
        @endif
    </x-card>
</div>
