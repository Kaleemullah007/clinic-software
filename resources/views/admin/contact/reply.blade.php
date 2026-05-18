<div class="modal fade" id="replyContact" tabindex="-1" aria-labelledby="replyContactLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="replyContactLabel">Reply</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="pb-3">
                    <label for="subject" class="form-label fs-6">Subject</label>
                    <input type="text"
                        class="form-control bg-grey border-secondary @error('subject') is-invalid @enderror"
                        id="subject" name="subject" value="{{ old('subject') }}"
                        autocomplete="subject" required>
                    @error('subject')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="pb-3">
                    <label for="message" class="form-label fs-6">Message</label>
                    <textarea type="text"
                        class="form-control bg-grey border-secondary @error('message') is-invalid @enderror"
                        id="message" name="message" value="{{ old('message') }}" rows="2"
                        autocomplete="message" required>I Want to discuss about hair transplant.</textarea>
                    @error('message')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-md ms-2" data-bs-dismiss="modal"><i class="bi bi-x-circle me-2"></i>Cancel</button>
                <button type="button" class="btn btn-success btn-md text-white"><i class="bi bi-save me-2"></i>Send</button>
            </div>
        </div>
    </div>
</div>
