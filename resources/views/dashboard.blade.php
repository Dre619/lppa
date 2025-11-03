<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl p-6">
        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @php
                $registrationTypes = \App\Models\RegistrationType::all();
            @endphp

            @foreach($registrationTypes as $types)
                @php
                    $count = \App\Models\Application::where('application_classification_id', $types->id)->count();
                @endphp

                <div class="relative overflow-hidden rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm transition hover:shadow-md dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            {{ $types->name }}
                        </h3>
                        <div class="text-sm rounded-full bg-blue-100 px-3 py-1 text-blue-600 dark:bg-blue-800 dark:text-blue-100">
                            {{ number_format($count) }}
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Total Applications
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
