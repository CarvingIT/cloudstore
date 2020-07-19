@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="driveform" title="Drive info" class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="/admin/drives/save">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>

                            <div class="col-md-6">
                                <select class="form-control" name="type">
                                    <option>Google Drive</option>
                                    <option>AWS S3</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="creds" class="col-md-4 col-form-label text-md-right">{{ __('Credentials') }}</label>

                            <div class="col-md-6">
                                <textarea id="creds" name="creds" class="form-control" value="{{ old('creds') }}" required></textarea>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <p></p>
            <div class="card">
                <div class="card-header">
                {{ __('Drives') }}
                <div class="icons" style="float:right;">
                    <a href="#" id="loadform"><span class="ui-icon ui-icon-plusthick"></span></a>
                </div>
                </div>
                <div class="card-body">
                    <table id="drives" class="display" style="width:100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ( $drives as $drive)
                            <tr>
                                <td>{{ $drive->name }}</td>
                                <td>{{ $drive->type }}</td>
                                <td>{{ $drive->created_at }}</td>
                                <td>
                                    <span class="ui-icon ui-icon-pencil"></span>
                                    <span class="ui-icon ui-icon-trash"></span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <script>
                    $(document).ready(function() {
                    $('#drives').DataTable();
                    } );
                    
                    $( function() {
                        dialog = $( "#driveform" ).dialog(
                            {
                                autoOpen: false,
                                height: 400,
                                width: 500,
                                modal: true,
                                /*
                                buttons: {
                                    "Add": addDrive,
                                    Cancel: function() {
                                    dialog.dialog( "close" );
                                    }
                                },
                                */
                            }
                        );
                        $( "#loadform" ).on( "click", function() {
                            dialog.dialog( "open" );
                        });
                    } );

                    </script>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
