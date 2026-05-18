<div class="row setting-row" id="setting-row{{$new_row}}" >
    <div class="col-lg-3 col-md-6 col-12 pt-3">
        <label for="key_name" class="form-label fs-6">Name</label>
        <input type="text"
            class="form-control bg-grey border-secondary @error('key_name') is-invalid @enderror"
            id="key_name" name="data[{{$new_row}}][key_name]" placeholder="Name" value="{{ old('key_name') }}"
            autocomplete="key_name" required>
        @error('key_name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-lg-3 col-md-6 col-12 pt-3">
        <label for="key_value" class="form-label fs-6">Value</label>
        <input type="text"
            class="form-control bg-grey border-secondary @error('key_value') is-invalid @enderror"
            id="key_value" name="data[{{$new_row}}][key_value]" placeholder="Value"
            value="{{ old('key_value') }}" autocomplete="key_value" required>
        @error('key_value')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-lg-3 col-md-6 col-12 pt-3">
        <span class='totalrecord-settings'></span>
        <label for="status" class="form-label fs-6">Status</label><br>
        <input type="checkbox" id="status{{$new_row}}" name="data[{{$new_row}}][status]"
         data-toggle="switchbutton" data-size="md" data-onstyle="success"
            data-offstyle="danger" data-onlabel="Active" data-offlabel="Inactive">
        @error('status')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    <div class="col-lg-3 col-md-6 col-12 pt-3 pt-5" id="setting-row{{$new_row}}-btn">
        <a href="#" class="btn btn-success " onclick="addSetting({{$new_row}})"><i class="bi bi-plus-lg"></i> Add</a>
    </div>
</div>
