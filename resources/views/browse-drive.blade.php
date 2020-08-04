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
            <div class="card">
                <div class="card-header"><a href="/home">Home</a> :: {{ __('Files in ') }}{{ $drive->name }}
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection