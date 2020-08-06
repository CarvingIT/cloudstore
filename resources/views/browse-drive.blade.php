@extends('layouts.app')

@section('content')
@push('scripts')
                    <script type="application/javascript">
                    $(document).ready(function() {
                        $('#cloudfiles').DataTable({
                        "aoColumnDefs": [
                            { "bSortable": false, "aTargets": [3]},
                            { "className": 'text-right', "aTargets": [1,2]},
                            { "className": 'td-actions text-right', "aTargets": [3]}
                        ],
                        "pagingType": "simple",
                        "info" : false,
                        "order": [[ 2, "desc" ]],
                        "serverSide":true,
                        "processing":true,
                        "ajax":'/list-files/{{ $drive->id }}',
                        "columns":[
                        {data:"filename"},
                        {data:"size", },
                         {data:"updated_at", },
                         {data:"actions"},
                        ]
                        });
                    } );
                    </script>
@endpush
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div id="shareform" title="Sharing file" class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="/drive/{{ $drive->id }}/share-file">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" value="" required>
                                <input id="file_id" name="file_id" type="hidden" value="" />
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Share') }}
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><a href="/home"><span class="ui-icon ui-icon-home"></span></a> :: {{ __('Browsing ') }}<strong>{{ $drive->name }}</strong>
                    <div class="card-header-icons">
                    <!--
                    <a href="/select-drive" title="Change drive"><span class="ui-icon ui-icon-disk"></span></a>
                    -->
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <table id="cloudfiles" class="table">
                        <thead class="text-primary">
                            <tr>
                            <th>Filename</th>
                            <th>Size</th>
                            <th>Modified</th>
                            <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                    </table>
@push('scripts')
                    <script>
                    $(document).ready( function() {
                        dialog = $( "#shareform" ).dialog(
                            {
                                autoOpen: false,
                                height: 200,
                                width: 500,
                                modal: true,
                            }
                        );
                    } );

                    function openShareDialog(file, id){
                            dialog.dialog({'title':'Sharing file '+file}).dialog('open');
                            $("#file_id").val(id);
                    }
                    </script>
@endpush
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
