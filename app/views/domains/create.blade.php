@extends('layouts.main')

@section('title')
Add Domain
@stop

@section('content')
    <div class="panel panel-primary">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    {{ Form::open(array('action' => 'DomainsController@store')) }}
                        {{ Form::label('domain') }}<br>
                        {{ Form::text('domain', '', array('class' => 'form-control')) }}
                        <br>
                        <br>
                        <button type="submit" class="btn btn-primary">Add</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
