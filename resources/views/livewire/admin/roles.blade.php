<?php

use Livewire\Volt\Component;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

new class extends Component {
    public $roles = null;

    // Form elements
    public string $name = '';
    public string $role_slug = '';
    public string $description = '';

    // Modal states
    public bool $showRoleModal = false;
    public $currentRole = null;

    public function mount()
    {
        $this->roles = Role::withCount('users')->get();
    }

    public function updatedName()
    {
        // Auto-generate slug when name changes
        if (!$this->currentRole) {
            $this->role_slug = Str::slug($this->name);
        }
    }

    public function openRoleModal($roleId = null)
    {
        $this->resetErrorBag();
        $this->reset(['name', 'role_slug', 'description']);
        
        if ($roleId) {
            $this->currentRole = Role::find($roleId);
            $this->name = $this->currentRole->name;
            $this->role_slug = $this->currentRole->role_slug;
            $this->description = $this->currentRole->description ?? '';
        } else {
            $this->currentRole = null;
        }
        
        $this->showRoleModal = true;
    }

    public function saveRole()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'role_slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->currentRole) {
            $rules['name'][] = Rule::unique(Role::class)->ignore($this->currentRole->id);
            $rules['role_slug'][] = Rule::unique(Role::class)->ignore($this->currentRole->id);
        } else {
            $rules['name'][] = Rule::unique(Role::class);
            $rules['role_slug'][] = Rule::unique(Role::class);
        }

        $validated = $this->validate($rules);

        if ($this->currentRole) {
            $this->currentRole->update($validated);
            
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Role updated successfully'
            );
        } else {
            Role::create($validated);
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'Role created successfully'
            );
    
        }

        $this->roles = Role::withCount('users')->get();
        $this->showRoleModal = false;
    }

    public function deleteRole($roleId)
    {
        $role = Role::withCount('users')->find($roleId);
        
        if ($role->users_count > 0) {
            
            $this->dispatch('toastMagic',
                status: 'error',
                title: 'Error',
                message: 'Cannot delete role with assigned users'
            );
            return;
        }

        $this->dialog()->confirm([
            'title'       => 'Delete Role',
            'description' => 'Are you sure you want to delete this role? This action cannot be undone.',
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Delete',
                'method' => 'confirmDeleteRole',
                'params' => $roleId,
            ],
            'reject' => [
                'label'  => 'Cancel',
            ],
        ]);
    }

    public function confirmDeleteRole($roleId)
    {
        $role = Role::find($roleId);
        $role->delete();
        
        $this->roles = Role::withCount('users')->get();
       
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'Role deleted successfully'
        );
    }

    public function generateSlug()
    {
        $this->role_slug = Str::slug($this->name);
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="Role Management" subtitle="Manage system roles and permissions">
        <x-slot name="action">
            <x-button primary wire:click="openRoleModal" icon="plus">
                Add Role
            </x-button>
        </x-slot>

        @if($roles->isEmpty())
                <x-button primary wire:click="openRoleModal" icon="plus">
                    Add Role
                </x-button>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($roles as $role)
                            <tr wire:key="role-{{ $role->id }}" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $role->name }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <x-badge flat blue label="{{ $role->role_slug }}" />
                                </td>
                                
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $role->description }}">
                                    {{ $role->description ?: '—' }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <x-badge flat positive label="{{ $role->users_count }} {{ Str::plural('user', $role->users_count) }}" />
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $role->created_at->format('M d, Y') }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <x-button 
                                        wire:click="openRoleModal({{ $role->id }})" 
                                        icon="pencil" 
                                        primary />
                                    
                                    <x-button 
                                        wire:click="deleteRole({{ $role->id }})" 
                                        icon="trash" 
                                        negative
                                        :disabled="$role->users_count > 0" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <!-- Role Modal -->
    <x-modal title="{{ $currentRole ? 'Edit Role' : 'Create Role' }}" wire:model.defer="showRoleModal">
        <x-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input 
                label="Name" 
                wire:model.live="name" 
                placeholder="Enter role name" />
            
            <div>
                <x-input 
                    label="Slug" 
                    wire:model="role_slug" 
                    placeholder="Enter role slug" />
                <x-button 
                    wire:click="generateSlug" 
                    flat 
                    small 
                    label="Generate from name" 
                    class="mt-1" />
            </div>
        </div>
        
        <x-textarea 
            label="Description" 
            wire:model="description" 
            placeholder="Enter role description (optional)" 
            class="mt-4" />
        
        <x-slot name="footer">
            <div class="flex justify-between">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button 
                    primary 
                    label="{{ $currentRole ? 'Update' : 'Create' }}" 
                    wire:click="saveRole" />
            </div>
        </x-slot>
        </x-card>
    </x-modal>
</div>