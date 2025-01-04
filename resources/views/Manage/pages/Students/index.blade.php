@extends('Manage.layouts.app')

@section('content')

    <div class="main-content" id="panel">
    @include('Manage.includes.header')
    <!-- Header -->
        <div class="header bg-primary">
            <div class="container-fluid">
                <div class="header-body">
                    <div class="row align-items-center py-4">
                        <div class="col-lg-6 col-7">
                            <h6 class="h2 text-white d-inline-block mb-0"> <a href="{{ route('dashboard') }}">Attendance</a></h6>
                            <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                                <ol class="breadcrumb breadcrumb-links breadcrumb-dark radius">
                                    <li class="breadcrumb-item"><i class="fas fa-users-class"></i></li>
                                    <li class="breadcrumb-item active">{{ $pageTitle }}</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-lg-6 col-5 text-right">
                            <button class="btn btn-sm btn-neutral"  data-toggle="modal" data-target="#createStudent"><i class="fas fa-plus mr-1"> </i> New</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-neutral" aria-label="Go to Dashboard">
                                <i class="fa fa-home" aria-hidden="true"></i>
                                <span class="sr-only">Dashboard</span>
                            </a><!-- Create Student Modal -->
                            @include('Manage.pages.Students.modals.CreateStudentModal')
                            <!--/ Create Student Modal -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Import Students</h6>
            </div>
            <form action="{{ route('import.students') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group row">

                        <div class="col-md-12 mb-3 mt-3">
                            <p>Please Upload CSV in Given Format <a href="{{ asset('files/sample-data-sheet.csv') }}" target="_blank">Sample CSV Format</a></p>
                        </div>
                        {{-- File Input --}}
                        <div class="col-sm-12 mb-3 mt-3 mb-sm-0">
                            <span style="color:red;">*</span>File Input(Datasheet)</label>
                            <input
                                type="file"
                                class="form-control form-control-user @error('file') is-invalid @enderror"
                                id="exampleFile"
                                name="file"
                                value="{{ old('file') }}">

                            @error('file')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-user float-right mb-3">Upload Students</button>
                </div>
            </form>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <!-- Table -->
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-header border-0">
                            <h3 class="mb-0">{{ $subTitle }}</h3>
                        </div>
                        <!-- Light table -->
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush datatable-buttons">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col" class="sort" data-sort="employee">Name</th>
                                    <th scope="col" class="sort" data-sort="employee">Email</th>
                                    <th scope="col" class="sort" data-sort="service">Phone</th>
                                    <th scope="col" class="sort" data-sort="action">Action</th>
                                </tr>
                                </thead>
                                <tbody class="list">
                                @foreach ($students as $student)
                                    <tr>
                                        <td class="text-capitalize">
                                            {{ $student->name }}
                                        </td>
                                        <td class="text-capitalize">
                                            {{ $student->email }}
                                        </td>
                                        <td class="text-md">
                                            {{ $student->phone }}
                                        </td>
                                        <td>
                                            <button data-toggle="modal" data-target="#updateStudent-{{ $student->id }}" class="btn btn-sm bg-green-500 text-white m-0 radius" title="edit">
                                                <i class="fas fa-edit" aria-hidden="true"></i>
                                            </button>
                                            <!-- Update Student Modal -->
                                            @include('Manage.pages.Students.modals.UpdateStudentModal', ['student' => $student])
                                            <!--/ Update Student Modal -->
                                            <a href="{{ route('student.show', $student) }}" class="btn btn-sm bg-blue-500 text-white m-0 radius" title="edit">
                                                <i class="fas fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <form action="{{ route('student.destroy', $student) }}" class="d-inline" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-sm bg-red-500 text-white radius" title="delete">
                                                    <i class="fas fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--/ Table -->
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')

@endpush
