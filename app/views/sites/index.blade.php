@extends('layouts.main')

@section('title')
Sites
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="btn-group">
          <a class="btn btn-primary" href="/sites/create">Add</a>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Site</th>
                            <th>Repository</th>
                            <th>Branch</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th colspan="4"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sites as $site)
                        <tr>
                            <td><a href="/sites/{{ $site->id }}">{{{ $site->getFullUrl() }}}</a></td>
                            <td>{{{ $site->repository->owner }}}/{{{ $site->repository->name }}}</td>
                            <td>{{{ $site->branch }}}</td>
                            <td>
                                {{{ $site->created_at->toFormattedDateString() }}} 
                                <br>
                                {{{ $site->created_at->toTimeString() }}}
                            </td>
                            <td>
                                {{{ $site->updated_at->toFormattedDateString() }}} 
                                <br>
                                {{{ $site->updated_at->toTimeString() }}}
                            </td>
                            <td>
                                <a href="http://{{ $site->getFullUrl() }}" target="_blank" class="btn btn-primary">Open Site</a>
                                <a title="Edit" class="btn btn-primary" href="{{ URL::action('SitesController@show', array('id' => $site->id)) }}">
                                    Edit
                                </a>
                                <a title="Rebuild Instance" class="btn btn-primary" href="{{ URL::action('SitesController@startInstance', array('id' => $site->id)) }}">
                                    Rebuild
                                </a>
                                {{ Form::open(
                                    array(
                                        'action' => array('SitesController@destroy', 'id' => $site->id),
                                        'class'  => 'display-inline',
                                        'method' => 'delete',
                                    )
                                ) }}
                                {{ Form::submit('Delete', array('class' => 'btn btn-danger')) }}
                                {{ Form::close() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
