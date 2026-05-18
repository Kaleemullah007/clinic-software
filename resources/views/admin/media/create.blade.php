<div class="modal fade" id="createMedia" tabindex="-1" aria-labelledby="createMediaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createMediaLabel">Create Media</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{-- form for for create Media --}}
            @include('flash-message')

            <form method="POST" action="{{ route('media.store') }}" enctype="">
                <div class="modal-body">
                    @csrf
                    <div class="pb-3">
                        <label for="type" class="form-label fs-6">Media Type</label><br>
                        <input type="checkbox" id="type" name="type" class="" data-width="100"
                            data-toggle="switchbutton" data-size="md" data-onstyle="success" data-offstyle="danger"
                            data-onlabel="Upload" data-offlabel="Link" checked>
                        @error('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="upload" class="form-label fs-6">Choose file</label>
                        <input type="file"
                            class="form-control bg-grey border-secondary @error('upload') is-invalid @enderror"
                            id="upload" name="upload" value="{{ old('upload') }}"
                            autocomplete="upload" required>
                        @error('upload')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="pb-3">
                        <label for="link" class="form-label fs-6">Attach Link</label>
                        <input type="url"
                            class="form-control bg-grey border-secondary @error('link') is-invalid @enderror"
                            id="link" name="link" placeholder="https://example.com" value="{{ old('link') }}"
                            autocomplete="link" required>
                        @error('link')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-md ms-2" data-bs-dismiss="modal"><i
                            class="bi bi-x-circle me-2"></i>Cancel</button>
                    <button type="submit" class="btn btn-success btn-md text-white"><i
                            class="bi bi-save me-2"></i>Save</button>
                </div>

            </form>
            {{-- end form for create media --}}

        </div>
    </div>
</div>
