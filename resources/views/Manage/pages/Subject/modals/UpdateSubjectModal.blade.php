<!DOCTYPE>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Course Modal</title>
</head>
<body>

<!-- Modal -->
<dialog id="updateSubject-{{ $subject->id }}" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update {{ $subject->name }}</h5>
                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('subject.update', $subject) }}">
                <div class="modal-body text-left">
                    @csrf
                    @method('PUT')
                    <h6 class="heading-small text-muted mb-4">Course information</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="name">Name*</label>
                                <input type="text" id="name" class="form-control name radius @error('name') is-invalid @enderror"
                                       value="{{ $subject->name }}" name="name" placeholder="Try Introduction to Java" required>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="description">Description</label>
                                <textarea type="text" id="description" class="form-control description radius @error('description') is-invalid @enderror"
                                          name="description" placeholder="Try 10" rows="6">{{ $subject->description }}</textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary radius" onclick="closeModal()">Close</button>
                    <button type="submit" class="btn btn-primary radius">Submit</button>
                </div>
            </form>
        </div>
    </div>
</dialog>

<script>
    // Function to open the modal
    function openModal() {
        document.getElementById('updateSubject-{{ $subject->id }}').showModal();
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('updateSubject-{{ $subject->id }}').close();
    }
</script>

</body>
</html>
