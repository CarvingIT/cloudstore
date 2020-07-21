@extends('layouts.app')

@section('content')
                                @php
                                    $settings = Auth::user()->settings->keyBy('key');
                                    $drives = $drives->keyBy('id');
                                @endphp
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Files in ') }}{{ $drives[$settings['current_drive']->value]->name }}
                
                    <div class="card-header-icons">
                    <a href="/select-drive" title="Change drive"><span class="ui-icon ui-icon-disk"></span></a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                                {{--
                                @foreach ($settings as $k=>$v)
                                    {{ $settings[$k]->value }}
                                @endforeach
                                --}}
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
