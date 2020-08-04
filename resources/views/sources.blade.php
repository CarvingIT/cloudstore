@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="sourceform" title="Source info" class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="/admin/source/save">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="type" class="col-md-4 col-form-label text-md-right">{{ __('Type') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="type">
                                    <option value="local">Local</option>
                                    <option value="ssh">Remote (Secure Shell)</option>
                                    <option value="ftp">Remote (FTP)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="path" class="col-md-4 col-form-label text-md-right">{{ __('Path') }}</label>
                            <div class="col-md-6">
                                <input id="path" type="text" class="form-control" name="path" value="{{ old('path') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="drive_id" class="col-md-4 col-form-label text-md-right">{{ __('Mapped Drive') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="drive_id">
                                @foreach($drives as $drive)
                                <option value="{{ $drive->id }}">{{ $drive->name }}</option>
                                @endforeach
                                </select>
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
            <div class="card">
                <div class="card-header">
                <a href="/admin/dashboard">Administration</a> :: {{ __('Sources') }}
                <div class="card-header-icons">
                    <a href="#" id="loadform"><span class="ui-icon ui-icon-plusthick"></span></a>
                </div>
                </div>
                <div class="card-body">
                    <table id="sources" class="display" style="width:100%">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ( $sources as $source)
                            <tr>
                                <td>{{ $source->name }}</td>
                                <td>{{ $source->type }}</td>
                                <td>{{ $source->created_at }}</td>
                                <td>
                                    <span class="ui-icon ui-icon-pencil"></span>
                                    <a href="/admin/source/delete/{{ $source->id }}"><span class="ui-icon ui-icon-trash"></span></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <script>
                    $(document).ready(function() {
                        $('#sources').DataTable( {
                            "columnDefs": [
                            { "orderable": false, "targets": 3 }
                            ]
                        });
                    } );
                    
                    $( function() {
                        dialog = $( "#sourceform" ).dialog(
                            {
                                autoOpen: false,
                                height: 350,
                                width: 500,
                                modal: true,
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
