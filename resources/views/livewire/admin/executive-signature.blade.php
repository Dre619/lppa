<?php

use Livewire\Volt\Component;
use App\Models\ExecutiveSignature;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;
    /*
    [
        'signature',
        'is_active'
    ]
    */
    public $signature = null;
    public $is_active = true;
    public $executiveSignatures = null;
    public bool $showModal = false;
    public $currentSignature = null;
    public bool $isEditing = false;
    public bool $showDeleteConfirmation = false;
    public $signatureId = null;

    public function mount()
    {
        $this->executiveSignatures = ExecutiveSignature::all();
    }
    public function openModal()
    {
        $this->resetErrorBag();
        $this->reset(['signature', 'is_active']);
        $this->showModal = true;
        $this->isEditing = false;
        $this->currentSignature = null;
    }
    public function openEditModal($signatureId)
    {
        $this->resetErrorBag();
        $this->reset(['signature', 'is_active']);
        $Signature = ExecutiveSignature::find($signatureId);
        Log::info(json_encode($Signature));
        if ($Signature) {
            $this->currentSignature = $Signature;
            //$this->signature = $Signature->signature;
            $this->is_active = $Signature->is_active == 1?true:false;
            $this->showModal = true;
            $this->isEditing = true;
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Executive signature not found'
            );
        }
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetErrorBag();
        $this->reset(['signature', 'is_active']);
        $this->currentSignature = null;
        $this->isEditing = false;
    }
    public function addSignature()
    {
        $validated = $this->validate([
            'signature' => 'required|image|max:1024', // 1MB Max
            'is_active' => 'boolean',
        ]);
        $validated['signature'] = $this->signature->store('signatures', 'public');
        ExecutiveSignature::create($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Executive signature added successfully'
        );
        $this->reset(['signature', 'is_active']);
        $this->executiveSignatures = ExecutiveSignature::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function updateSignature()
    {
        $validated = $this->validate([
            'signature' => 'required|image|max:1024', // 1MB Max
            'is_active' => 'boolean',
        ]);
        if ($this->signature) {
            $validated['signature'] = $this->signature->store('signatures', 'public');
        } else {
            unset($validated['signature']);
        }
        $this->currentSignature->update($validated);
        $this->dispatch('toastMagic',
            status: 'success', title: 'Success',
            message: 'Executive signature updated successfully'
        );
        $this->reset(['signature', 'is_active']);
        $this->executiveSignatures = ExecutiveSignature::all();
        $this->showModal = false;
        $this->isEditing = false;
    }
    public function deleteSignature($signatureId)
    {
        $this->signatureId = $signatureId;
        $this->showDeleteConfirmation = true;
    }
    public function confirmDelete()
    {
        $signature = ExecutiveSignature::find($this->signatureId);
        if ($signature) {
            $signature->delete();
            $this->dispatch('toastMagic',
                status: 'success', title: 'Success',
                message: 'Executive signature deleted successfully'
            );
            $this->executiveSignatures = ExecutiveSignature::all();
            $this->showDeleteConfirmation = false;
        } else {
            $this->dispatch('toastMagic',
                status: 'error', title: 'Error',
                message: 'Executive signature not found'
            );
            $this->showDeleteConfirmation = false;
        }
        $this->signatureId = null;
        $this->reset(['signatureId']);
    }
    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->resetErrorBag();
        $this->reset(['signatureId']);
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Executive Signature" class="mt-auto">
        <x-slot name="action">
            <x-button wire:click="openModal" primary>Add Signature</x-button>
        </x-slot>

        <table class="min-w-full divide-y divide-gray-200 mt-4">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signature</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($executiveSignatures as $signature)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <img src="{{ asset('storage/' . $signature->signature) }}" alt="Signature" class="h-10 w-auto">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $signature->is_active ? 'Yes' : 'No' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-button wire:click="openEditModal({{ $signature->id }})" secondary>Edit</x-button>
                            <x-button wire:click="deleteSignature({{ $signature->id }})" danger>Delete</x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($showModal)
            <x-modal wire:model.defer="showModal">
                <x-card title="{{ $isEditing ? 'Edit Signature' : 'Add Signature' }}">
                    <form wire:submit.prevent="{{ $isEditing ? 'updateSignature' : 'addSignature' }}" enctype="multipart/form-data">
                        <div class="mb-4">
                             <span wire:loading wire:target="signature" class="text-blue-500 text-sm mt-1">
                                    Uploading file...
                                </span>

                               
                            <x-input label="Signature Image" type="file" wire:model.defer="signature" id="signature" accept="image/*" 
                            />
                            @error('signature')<span class="text-red-600">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-4">
                            <x-checkbox
                                label="Active"
                                wire:model.defer="is_active"
                                class="mt-2"
                            />
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <x-button type="button" wire:click="closeModal" secondary>Cancel</x-button>
                            <x-button type="submit" primary>{{ $isEditing ? 'Update' : 'Add' }}</x-button>
                        </div>
                    </form>
                </x-card>
            </x-modal>
        @endif
        @if($showDeleteConfirmation)
            <x-modal wire:model.defer="showDeleteConfirmation" title="Confirm Deletion">
                <x-card>
                    <p>Are you sure you want to delete this executive signature?</p>
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-button wire:click="cancelDelete" secondary>Cancel</x-button>
                        <x-button wire:click="confirmDelete" danger>Delete</x-button>
                    </div>
                </x-card>
            </x-modal>
        @endif
    </x-card>
                
</div>
