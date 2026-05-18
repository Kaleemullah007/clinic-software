<option>{{ __('en.Choose') }}</option>
@foreach ($services as $service)
    <option value="{{ $service->id }}">{{ $service->name }}</option>
@endforeach
