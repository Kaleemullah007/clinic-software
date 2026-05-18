<div class="setting-row row d-flex " id="setting-row{{ $new_row }}">
    <span class='totalrecord-settings'></span>
    <div class="col-lg-4 col-md-6 col-12 pt-1">
        <label for="service_id" class="form-label fs-6">{{ __('en.Service') }}</label>
        <select class="form-select select2 border-dark @error('service_id') is-invalid @enderror"
            name="service_id[{{ $new_row }}][service_id]" id="{{ $new_row }}-service_id"
            autocomplete="service_id" required onchange="getPrice({{ $new_row }})">
            <option>{{ __('en.Choose') }}</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}" @if (old('service_id') == $service->id) selected @endif>
                    {{ $service->name }}
                </option>
            @endforeach

        </select>
        @error('service_id')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-lg-2 col-md-6 col-12 pt-1">
        <label for="price" class="form-label fs-6">{{ __('en.Price') }}</label>
        <input type="number" min="1"
            class="form-control calculation mb-2 border-dark @error('price') is-invalid @enderror"
            id="{{ $new_row }}-price" name="service_id[{{ $new_row }}][price]" placeholder="10"
            value="{{ old('price') }}" autocomplete="price" autofocus onkeyup="calcualtePrice()" min="0">
        @error('price')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-lg-2 col-md-6 col-12 d-flex align-items-end mb-2" id="setting-row{{ $new_row }}-btn">
        <a href="javascript:void(0)" class="btn btn-success " id="setting-row{{ $new_row }}-href"
            onclick="addSetting({{ $new_row }})"><i class="bi bi-plus-lg"></i></a>
    </div>
</div>
