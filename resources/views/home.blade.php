@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard
                    <!--
                    <div class="card-header-icons">
                    <a href="/select-drive" title="Change drive"><span class="ui-icon ui-icon-disk"></span></a>
                    </div>
                    -->
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h4>Cloud Drives</h4>
                    <ul>
                        @foreach ($drives as $d)
                        <li><a href="/browse-drive/{{ $d->id }}">{{ $d->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
