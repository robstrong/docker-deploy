@extends('layouts.main')

@section('title')
Add Site
@stop

@section('content')
    <div class="panel panel-primary">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    {{ Form::open(array('action' => 'SitesController@store')) }}
                        {{ Form::label('Domain') }}<br>
                        {{ Form::select('domain_id', $domainDropdown) }}<br><br>
                        {{ Form::label('Subdomain') }}<br>
                        {{ Form::text('subdomain', '', array('class' => 'form-control')) }}<br>
                        {{ Form::label('Repository') }}<br>
                        {{ Form::select('repository', $repoDropdown) }}<br>
                        <br>
                        {{ Form::label('Branch') }}<br>
                        <select name="branch">
                            <option value="master">master</option>
                        </select>
                        <br>
                        <br>
                        <button type="submit" class="btn btn-primary">Add</button>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@stop
