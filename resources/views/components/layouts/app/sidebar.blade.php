<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        {!! ToastMagic::styles() !!}
    </head>
    @php
        $application_data = [
            1 => 'change-of-land-use',
            2 => 'development',
            3 => 'sub-division'
        ];
    @endphp
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                </flux:navlist.group>
                @if(in_array('user management',auth()->user()->getRole()))
                <flux:navlist.group :heading="'User Management'" :expandable="true" :expanded="request()->routeIs('admin.users')||request()->routeIs('admin.roles')?true:false" class="grid">
                    <flux:navlist.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    <flux:navlist.item icon="shield-check" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>{{ __('Roles') }}</flux:navlist.item>
                </flux:navlist.group>
                @endif
                @if(in_array('applications',auth()->user()->getRole()) || in_array('change-of-land-use',auth()->user()->getRole()))
                <flux:navlist.group :heading="'Applications'" :expandable="true" :expanded="request()->routeIs('admin.applications')?true:false" class="grid">
                    @php
                        $registrationTypes = \App\Models\RegistrationType::all();
                    @endphp
                    @foreach($registrationTypes as $registrationType)
                        @if(in_array($application_data[$registrationType->id],auth()->user()->getRole())|| in_array('super-admin',auth()->user()->getRole()))
                        <flux:navlist.item
                            icon="document-text"
                            :href="route('admin.applications', ['registrationTypeId' => $registrationType->id])"
                            :current="request()->routeIs('admin.applications') && request()->route('registrationTypeId') == $registrationType->id"
                            wire:navigate
                        >
                            {{ $registrationType->name }}
                        </flux:navlist.item>
                        @endif
                    @endforeach
                </flux:navlist.group>
                @endif
                @if(in_array('schedules',auth()->user()->getRole()))
                <flux:navlist.group :heading="'Schedules & Notices'" :expandable="true" :expanded="request()->routeIs('admin.schedules')?true:false" class="grid">
                    @php
                        $registrationTypes = \App\Models\RegistrationType::all();
                    @endphp

                    @foreach($registrationTypes as $registrationType)
                        @if(in_array($application_data[$registrationType->id],auth()->user()->getRole())|| in_array('super-admin',auth()->user()->getRole()))
                            @if($registrationType->id != 1)
                                <flux:navlist.item
                                    icon="document-text"
                                    :href="route('admin.schedules', ['registrationTypeId' => $registrationType->id])"
                                    :current="request()->routeIs('admin.schedules') && request()->route('registrationTypeId') == $registrationType->id"
                                    wire:navigate
                                >
                                    {{ $registrationType->name }}
                                </flux:navlist.item>
                            @endif
                        @endif
                    @endforeach
                </flux:navlist.group>
                @endif
                @if(in_array('field-data',auth()->user()->getRole()) || in_array('super-admin',auth()->user()->getRole()))
                <flux:navlist.item :current="request()->routeIs('admin.building-controls')" icon="document-text" :href="route('admin.building-controls')" wire:navigate>{{ __('Bulding Controls') }}</flux:navlist.item>
                @endif
            </flux:navlist>

            <flux:spacer />

            @if(in_array('system settings',auth()->user()->getRole()))
            <flux:navlist variant="outline">
                <flux:navlist.group :expandable="true" :expanded="request()->routeIs('admin.districts')||request()->routeIs('admin.registration-areas')||request()->routeIs('admin.development-areas')||request()->routeIs('admin.resolutions')||request()->routeIs('admin.application-classes')||request()->routeIs('admin.registration-types')
                ||request()->routeIs('admin.sub-areas')||
                request()->routeIs('admin.titles')||request()->routeIs('admin.construction-stages')||request()->routeIs('admin.property-types')||request()->routeIs('admin.change-use-stages')||request()->routeIs('admin.land-uses')
                ||request()->routeIs('admin.executive-signatures')||request()->routeIs('admin.registration-organizations')||request()->routeIs('admin.users')||request()->routeIs('admin.roles')?true:false" :heading="__('System Settings')" class="grid">
                    <flux:navlist.item icon="building-office-2" :href="route('admin.districts')" :current="request()->routeIs('admin.districts')" wire:navigate>{{ __('Districts') }}</flux:navlist.item>
                    <flux:navlist.item icon="map-pin" :href="route('admin.registration-areas')" :current="request()->routeIs('admin.registration-areas')" wire:navigate>{{ __('Registration Areas') }}</flux:navlist.item>
                    <flux:navlist.item icon="map" :href="route('admin.development-areas')" :current="request()->routeIs('admin.development-areas')" wire:navigate>{{ __('Development Areas') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.resolutions')" :current="request()->routeIs('admin.resolutions')" wire:navigate>{{ __('Resolutions') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.application-classes')" :current="request()->routeIs('admin.application-classes')" wire:navigate>{{ __('Application Classes') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.registration-types')" :current="request()->routeIs('admin.registration-types')" wire:navigate>{{ __('Registration Types') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.sub-areas')" :current="request()->routeIs('admin.sub-areas')" wire:navigate>{{ __('Sub Areas') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.titles')" :current="request()->routeIs('admin.titles')" wire:navigate>{{ __('Titles') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.construction-stages')" :current="request()->routeIs('admin.construction-stages')" wire:navigate>{{ __('Construction Stages') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.property-types')" :current="request()->routeIs('admin.property-types')" wire:navigate>{{ __('Property Types') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.change-use-stages')" :current="request()->routeIs('admin.change-use-stages')" wire:navigate>{{ __('Change of Use Stages') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.land-uses')" :current="request()->routeIs('admin.land-uses')" wire:navigate>{{ __('Land Uses') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.executive-signatures')" :current="request()->routeIs('admin.executive-signatures')" wire:navigate>{{ __('Executive Signatures') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.registration-organizations')" :current="request()->routeIs('admin.registration-organizations')" wire:navigate>{{ __('Registration Org') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-text" :href="route('admin.roles')" :current="request()->routeIs('admin.roles')" wire:navigate>{{ __('User Roles') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>
            @endif
            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        {!! ToastMagic::scripts() !!}
    </body>
</html>
