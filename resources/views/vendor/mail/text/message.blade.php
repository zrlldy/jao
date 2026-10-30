@props(['title' => null])
<x-mail::layout>
    <x-slot:header>
        <x-mail::header>
            East Equator Express
        </x-mail::header>
    </x-slot:header>

    @if ($title)
    {{ $title }}

    @endif
    {{ $slot }}

    @isset($subcopy)
        <x-slot:subcopy>
            <x-mail::subcopy>
                {{ $subcopy }}
            </x-mail::subcopy>
        </x-slot:subcopy>
    @endisset

    <x-slot:footer>
        <x-mail::footer>
            East Equator Express
            Mabolo, Cebu City, Philippines 6000
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
