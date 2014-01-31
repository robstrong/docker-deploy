<?php

use Symfony\Component\Yaml\Yaml;

class RepositoriesController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('repositories.index', array('repos' => \Repository::with('user')->orderBy('created_at', 'DESC')->get()));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $github = App::make('github');
        $orgs = $github->api('current_user')->organizations();
        foreach ($orgs as &$org) {
            $org['repos'] = $github->api('organization')->repositories($org['login']);
        }
        return View::make(
            'repositories.create',
            array(
                'orgs'  => $orgs,
                'repos' => $github->api('current_user')->repositories(),
            )
        );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$github = App::make('github');
        $repo = $github->api('repo')->show(Input::get('owner'), Input::get('repo'));
        $record = new Repository;
        $record->repository = $repo['ssh_url'];
        $record->user_id = Auth::user()->id;
        if (Input::get('config_override')) {
            $record->config_override = Input::get('config_override');
        } else {
            $record->config_override = "tests:
    php_code_sniffer:
        standard: \"PSR2\"
        directory:
            - \"application/models\"
            - \"application/controllers\"";
        }
        $record->save();

        $record->installGithubPushHook(Input::get('owner'), Input::get('repo'));
        Session::flash('success', 'Repository Added');
        return Redirect::action('RepositoriesController@index');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make(
            'repositories.show', 
            array(
                'repo'     => \Repository::with('user')->findOrFail($id)
            )
        );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make(
            'repositories.edit', 
            array(
                'repo'     => \Repository::findOrFail($id)
            )
        );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        //check that yaml is valid
        try {
            Yaml::parse(Input::get('config_override'));
        } catch (\Exception $e) {
            Session::flash('error', 'Invalid Yaml Submitted');
            return Redirect::route('repositories.edit', array($id));
        }
        $repo = Repository::findOrFail($id);
        $repo->config_override = Input::get('config_override');
        $repo->save();
        Session::flash('success', 'Repository Updated');
        return Redirect::route('repositories.show', array($id));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    public function installWebhooks($id)
    {
        $repo = Repository::find($id);
        $repo->installGithubPushHook();
        Session::flash('success', 'Github Webhook Installed');
        return Redirect::route('repositories.index');
    }

}
