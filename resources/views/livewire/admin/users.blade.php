<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use WireUi\Traits\WireUiActions;

new class extends Component {
    use WireUiActions;
    public $users = null;
    public $roles = null;

    // Form elements
    public string $name = '';
    public string $email = '';
    public ?int $role_id = null;
    public string $password = '';

    // Modal states
    public bool $showUserModal = false;
    public $currentUser = null;

    public function mount()
    {
        $this->users = User::with('role')->get();
        $this->roles = Role::all();
    }

    public function openUserModal($userId = null)
    {
        $this->resetErrorBag();
        $this->reset(['name', 'email', 'role_id', 'password']);
        
        if ($userId) {
            $this->currentUser = User::find($userId);
            $this->name = $this->currentUser->name;
            $this->email = $this->currentUser->email;
            $this->role_id = $this->currentUser->role_id;
        } else {
            $this->currentUser = null;
        }
        
        $this->showUserModal = true;
    }

    public function saveUser()
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'role_id' => ['required', 'exists:' . Role::class . ',id'],
        ];

        if ($this->currentUser) {
            $rules['email'][] = Rule::unique(User::class)->ignore($this->currentUser->id);
            $rules['password'] = ['nullable', 'string', 'min:8'];
        } else {
            $rules['email'][] = Rule::unique(User::class);
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        $validated = $this->validate($rules);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } elseif ($this->currentUser) {
            unset($validated['password']);
        }

        if ($this->currentUser) {
            $this->currentUser->update($validated);
           
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'User updated successfully'
            );
        } else {
            User::create($validated);
            
            $this->dispatch('toastMagic',
                status: 'success',
                title: 'Success',
                message: 'User created successfully'
            );
        }

        $this->users = User::with('role')->get();
        $this->showUserModal = false;
    }

    public function deleteUser($userId)
    {
        $this->dialog()->confirm([
            'title'       => 'Delete User',
            'description' => 'Are you sure you want to delete this user? This action cannot be undone.',
            'icon'        => 'error',
            'accept'      => [
                'label'  => 'Delete',
                'method' => 'confirmDeleteUser',
                'params' => $userId,
            ],
            'reject' => [
                'label'  => 'Cancel',
            ],
        ]);
    }

    public function confirmDeleteUser($userId)
    {
        $user = User::find($userId);
        $user->delete();
        
        $this->users = User::with('role')->get();
        
        $this->dispatch('toastMagic',
            status: 'success',
            title: 'Success',
            message: 'User deleted successfully'
        );
    }
}; ?>

<div class="mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <x-card title="User Management" subtitle="Manage system users and access">
        <x-slot name="action">
            <x-button label="Add User" primary wire:click="openUserModal" icon="plus">
                Add User
            </x-button>
        </x-slot>

        @if($users->isEmpty())
            <div class="text-center py-8">
                <p class="text-gray-500">No users found. Click the button above to add a new user.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class=" divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <x-avatar class="w-8 h-8 rounded-full mr-3" label="{{ strtoupper(substr($user->name, 0, 1)) }}" />
                                        <span class="font-medium">{{ $user->name }}</span>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-badge flat blue label="{{ $user->role->name ?? 'No Role' }}" />
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <x-button 
                                        wire:click="openUserModal({{ $user->id }})" 
                                        icon="pencil" 
                                        primary />
                                    
                                    <x-button 
                                        wire:click="deleteUser({{ $user->id }})" 
                                        icon="trash" 
                                        negative />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>

    <!-- User Modal -->
    <x-modal title="{{ $currentUser ? 'Edit User' : 'Create User' }}" wire:model.defer="showUserModal">
        <x-card>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input 
                label="Name" 
                wire:model="name" 
                placeholder="Enter user's full name" />
            
            <x-input 
                label="Email" 
                wire:model="email" 
                placeholder="Enter user's email" />
            <x-select 
                label="Role" 
                wire:model="role_id" 
                placeholder="Select a role"
                :options="$roles" 
                option-label="name"
                option-value="id"
                />
            
            <x-input 
                label="Password {{ $currentUser ? '(leave blank to keep current)' : '' }}" 
                wire:model="password" 
                type="password"
                placeholder="Enter password" />
        </div>
        
        <x-slot name="footer">
            <div class="flex justify-between">
                <x-button flat label="Cancel" x-on:click="close" />
                <x-button 
                    primary 
                    label="{{ $currentUser ? 'Update' : 'Create' }}" 
                    wire:click="saveUser" />
            </div>
        </x-slot>
        </x-card>
    </x-modal>
</div>