@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="sourceform" title="Configure a directory on this server for back up" class="card">
                <div class="card-body">
                    <form method="POST" action="/admin/source/save">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="" placeholder="Some unique name" required autofocus>
                                <input type="hidden" name="type" value="local" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="path" class="col-md-4 col-form-label text-md-right">{{ __('Path') }}</label>
                            <div class="col-md-6">
                                <input id="path" type="text" class="form-control" name="path" value="{{ old('path') }}" placeholder="/home/username/your_dir" required>
                            </div>
                        </div>
                        <div class="form-group row sshfield">
                            <label for="server" class="col-md-4 col-form-label text-md-right">{{ __('Server') }}</label>
                            <div class="col-md-6">
                                <input id="server" type="text" class="form-control" name="server" value="" placeholder="FQDN or IP Address">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="drive_id" class="col-md-4 col-form-label text-md-right">{{ __('Mapped Drive') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="drive_id" required>
                                <option value="">Select a drive</option>
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

            <div id="sourceformssh" title="Configure back up of remote directory over SSH" class="card">
                <div class="card-body">
                    <form method="POST" action="/admin/source/save">
                        @csrf
                        <div class="form-group row">
                            <label for="namessh" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="namessh" type="text" class="form-control" name="name" value="" required>
                                <input type="hidden" name="type" value="ssh" />
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <label for="serverssh" class="col-md-4 col-form-label text-md-right">{{ __('Server address') }}</label>
                            <div class="col-md-6">
                                <input id="serverssh" type="text" class="form-control" name="serverssh" value="" placeholder="FQDN or IP Address" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="portssh" class="col-md-4 col-form-label text-md-right">{{ __('Port') }}</label>
                            <div class="col-md-6">
                                <input id="portssh" type="text" class="form-control" name="portssh" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="usernamessh" class="col-md-4 col-form-label text-md-right">{{ __('SSH Username') }}</label>
                            <div class="col-md-6">
                                <input id="usernamessh" type="text" class="form-control" name="usernamessh" value="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="passwordssh" class="col-md-4 col-form-label text-md-right">{{ __('SSH Password') }}</label>
                            <div class="col-md-6">
                                <input id="passwordssh" type="password" class="form-control" name="passwordssh" value="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pathssh" class="col-md-4 col-form-label text-md-right">{{ __('Remote directory') }}</label>
                            <div class="col-md-6">
                                <input id="pathssh" type="text" class="form-control" name="pathssh" value="" required>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <label for="drive_idssh" class="col-md-4 col-form-label text-md-right">{{ __('Mapped Drive') }}</label>
                            <div class="col-md-6">
                                <select class="form-control" id="drive_idssh" name="drive_id" required>
                                <option value="">Select a drive</option>
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

            <div id="sourceformftp" title="Configure remote directory for back up over FTP" class="card">
                <div class="card-body">
                    <form method="POST" action="/admin/source/save">
                        @csrf
                        <div class="form-group row">
                            <label for="nameftp" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                            <div class="col-md-6">
                                <input id="nameftp" type="text" class="form-control" name="name" value="" required>
                                <input type="hidden" name="type" value="ftp" />
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <label for="serverftp" class="col-md-4 col-form-label text-md-right">{{ __('Server address') }}</label>
                            <div class="col-md-6">
                                <input id="serverftp" type="text" class="form-control" name="serverftp" value="" placeholder="FQDN or IP Address" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="portftp" class="col-md-4 col-form-label text-md-right">{{ __('Port') }}</label>
                            <div class="col-md-6">
                                <input id="portftp" type="text" class="form-control" name="portftp" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="usernameftp" class="col-md-4 col-form-label text-md-right">{{ __('FTP Username') }}</label>
                            <div class="col-md-6">
                                <input id="usernameftp" type="text" class="form-control" name="usernameftp" value="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="passwordftp" class="col-md-4 col-form-label text-md-right">{{ __('FTP Password') }}</label>
                            <div class="col-md-6">
                                <input id="passwordftp" type="password" class="form-control" name="passwordftp" value="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pathftp" class="col-md-4 col-form-label text-md-right">{{ __('Remote directory') }}</label>
                            <div class="col-md-6">
                                <input id="pathftp" type="text" class="form-control" name="pathftp" value="" required>
                            </div>
                        </div>
                        <hr />
                        <div class="form-group row">
                            <label for="drive_idftp" class="col-md-4 col-form-label text-md-right">{{ __('Mapped Drive') }}</label>
                            <div class="col-md-6">
                                <select id="drive_idftp" class="form-control" name="drive_id" required>
                                <option value="">Select a drive</option>
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
                    <a href="#" id="loadform"><img src="/img/new_folder.png" /></a>
                    <a href="#" id="loadformftp"><img src="/img/ftp.png" /></a>
                    <a href="#" id="loadformssh"><img src="/img/ssh.png" /></a>
                </div>
                </div>
                <div class="card-body">
                @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if( Session::has('alert-' . $msg) )
                    <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }}</p>
                    @endif
                @endforeach
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
                        // hide all ssh fields
                        $('.sshfield').hide();
    
                    } );
                    
                    $( function() {
                        dialog = $( "#sourceform" ).dialog(
                            {
                                autoOpen: false,
                                height: 300,
                                width: 500,
                                modal: true,
                            }
                        );
                        dialogssh = $( "#sourceformssh" ).dialog(
                            {
                                autoOpen: false,
                                height: 550,
                                width: 500,
                                modal: true,
                            }
                        );
                        dialogftp = $( "#sourceformftp" ).dialog(
                            {
                                autoOpen: false,
                                height: 550,
                                width: 500,
                                modal: true,
                            }
                        );

                        $( "#loadform" ).on( "click", function() {
                            dialog.dialog( "open" );
                        });
                        $( "#loadformssh" ).on( "click", function() {
                            dialogssh.dialog( "open" );
                        });
                        $( "#loadformftp" ).on( "click", function() {
                            dialogftp.dialog( "open" );
                        });
                    } );

                    </script>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
