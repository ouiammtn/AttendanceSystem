<!DOCTYPE>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accessible Modal Example</title>
</head>
<body>

<!-- Modal Dialog Example -->
<dialog id="createAttendance" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Start new Attendance</h5>
                <button type="button" class="close" onclick="closeModal()" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="{{ route('attendance.store') }}">
                <div class="modal-body text-left">
                    @csrf
                    <h6 class="heading-small text-muted mb-4">Attendance information</h6>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="subject">Select Subject*</label>
                                <select id="subject" name="subject_id" class="form-control radius">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{$subject->id}}">{{$subject->name}}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-control-label" for="input-date">Choose Date*</label>
                                <input class="form-control datepicker @error('date') is-invalid @enderror " name="date" id="input-date" placeholder="Select date" type="text" value="{{ \Carbon\Carbon::today()->format('m/d/Y') }}">
                                @error('date')
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
    // Function to open the dialog
    function openModal() {
        document.getElementById('createAttendance').showModal();
    }

    // Function to close the dialog
    function closeModal() {
        document.getElementById('createAttendance').close();
    }
</script>

</body>
</html>
