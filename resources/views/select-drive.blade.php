@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cloud Storage') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="/set-drive">
                        @csrf

                        <div class="form-group row">
                            <label for="drive" class="col-md-4 col-form-label text-md-right">{{ __('Select Drive') }}</label>

                            <div class="col-md-6">
                                <select name="drive" class="form-control">
                                @foreach ($drives as $k=>$v)
                                    <option value="{{ $k }}">{{ $drives[$k]->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Set') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
