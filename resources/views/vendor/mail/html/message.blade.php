@props(['title' => null, 'mailMessage' => null])
<x-mail::layout :title="$title">
<x-slot:header>
<x-mail::header :mail-message="$mailMessage" />
</x-slot:header>

{{ $slot }}

@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

<x-slot:footer>
<x-mail::footer />
</x-slot:footer>
</x-mail::layout>
