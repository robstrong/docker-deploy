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
                            <th>Start Instances</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sites as $site)
                        <tr>
                            <td><a href="/sites/{{ $site->id }}">{{{ $site->getFullUrl() }}}</a></td>
                            <td>{{{ $site->repository->owner }}}/{{{ $site->repository->name }}}</td>
                            <td>{{{ $site->branch }}}</td>
                            <td>{{ $site->created_at }}</td>
                            <td>{{ $site->updated_at }}</td>
                            <td><a href="{{ URL::action('SitesController@startInstance', array('id' => $site->id)) }}"><i class="fa fa-bolt"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop
