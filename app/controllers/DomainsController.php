<?php

class DomainsController extends \BaseController
{
    public function index()
    {
        return View::make(
            'domains.index',
            array(
                'domains' => Domain::orderBy('domain')->get()
            )
        );
    }

    public function create()
    {
        return View::make(
            'domains.create'
        );
    }

    public function store()
    {
        $rules = array(
            'domain' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::action('DomainsController@create')->withErrors($validator);
        }

        $domain = new Domain;
        $domain->domain = Input::get('domain');
        $domain->save();

        Session::flash('success', 'Domain successfully created');
        return Redirect::action('DomainsController@index');
    }
}
