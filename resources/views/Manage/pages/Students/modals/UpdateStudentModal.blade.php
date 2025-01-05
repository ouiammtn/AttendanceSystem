<!DOCTYPE>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student Modal</title>
</head>
<body>

<!-- Modal -->
<dialog id="updateStudent-{{ $student->id }}" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update {{ $student->name }}</h5>
                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('student.update', $student) }}">
                <div class="modal-body text-left">
                    @csrf
                    @method('PUT')
                    <h6 class="heading-small text-muted mb-4">Student information</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="name">Name*</label>
                                <input type="text" id="name" class="form-control name radius @error('name') is-invalid @enderror"
                                       value="{{ $student->name }}" name="name" placeholder="Try John Snow" required>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="email">Email*</label>
                                <input type="email" id="email" class="form-control email radius @error('email') is-invalid @enderror"
                                       value="{{ $student->email }}" name="email" placeholder="Try john@attendance.com" required>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="phone">Phone number*</label>
                                <input type="text" id="phone" class="form-control phone radius @error('phone') is-invalid @enderror"
                                       value="{{ $student->phone }}" name="phone" placeholder="Try +961 01 123456" required>
                                @error('phone')
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
        document.getElementById('updateStudent-{{ $student->id }}').showModal();
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('updateStudent-{{ $student->id }}').close();
    }
</script>

</body>
</html>
