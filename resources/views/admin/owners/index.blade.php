<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    エロクアント
                    @foreach($e_all as $e_owner)
                    {{$e_owner->name}}<br>
                    {{$e_owner->created_at->diffForHumans()}}<br>
                    @endforeach
                    <br>クエリビルダ
                    @foreach($q_get as $q_owner)
                    {{$q_owner->name}}<br>
                    {{Carbon\Carbon::parse($q_owner->created_at)->diffForHumans()}}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
