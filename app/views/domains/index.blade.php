@extends('layouts.main')

@section('title')
Domains
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="btn-group">
          <a class="btn btn-primary" href="{{ URL::action('DomainsController@create') }}">Add</a>
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
                            <th>Domain</th>
                            <th>Created</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($domains as $domain)
                        <tr>
                            <td><a href="{{ URL::action('DomainsController@show', array('id' => $domain->id )) }}">{{{ $domain->domain }}}</a></td>
                            <td>{{ $domain->created_at }}</td>
                            <td>{{ $domain->updated_at }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

