Thank you for your request.

@foreach ($formData as $key => $item)
@if ($key != '_token')
{{ ucwords(str_replace('_', ' ', $key)) }}: {{ $item }}

@endif
@endforeach
