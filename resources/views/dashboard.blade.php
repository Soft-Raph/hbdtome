<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Balance') }} :{{$balance}}
            </h2>
        </div>
    </x-slot>
    <!-- component -->
    <h3 class="text-2xl text-gray-700 font-bold mb-6 ml-3 flex justify-center">My Wishes</h3>
    <div class="px-6 py-6">
        <ol  class="flex flex-wrap">
            @foreach($wishes as $wish)
            <li class="border-l-2 border-green-600">
                <div class="md:flex flex-start">
                    <div class="bg-green-600 w-6 h-6 flex items-center justify-center rounded-full -ml-3.5">
                        <svg aria-hidden="true" focusable="false" data-prefix="fas" class="text-white w-3 h-3" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                            <path fill="currentColor" d="M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm64-192c0-8.8 7.2-16 16-16h288c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16v-64zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"></path>
                        </svg>
                    </div>
                    <div class="block p-6 rounded-lg shadow-lg bg-gray-100 max-w-md ml-6 mb-10">
                        <div class="flex justify-between mb-4">
                            <a href="#!" class="font-medium text-purple-600 hover:text-purple-700 focus:text-purple-800 duration-300 transition ease-in-out text-sm">{{$wish->name}}</a>
                            <a href="#!" class="font-medium text-purple-600 hover:text-purple-700 focus:text-purple-800 duration-300 transition ease-in-out text-sm">{{$wish->created_at}}</a>
                        </div>
                        <p class="text-gray-700 mb-6">{{$wish->message}}</p>
                        <div class="flex justify-between mb-4">
                        <a href="#!" class="font-medium text-purple-600 hover:text-purple-700 focus:text-purple-800 duration-300 transition ease-in-out text-sm">{{$wish->number}}</a>
                            <a href="#!" class="font-medium text-purple-600 hover:text-purple-700 focus:text-purple-800 duration-300 transition ease-in-out text-sm">Got Card: {{$wish->fund}}</a>
                        </div>
                    </div>
                </div>
            </li>
            @endforeach
        </ol>
    </div>
</x-app-layout>
