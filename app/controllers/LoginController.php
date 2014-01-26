<?php

use OAuth\OAuth2\Service\GitHub;
use OAuth\Common\Consumer\Credentials;

class LoginController extends \BaseController
{
    protected $githubOauthService;

    public function getIndex()
    {
        if (Auth::check()) {
            return Redirect::route('home');
        }

        return View::make(
            'login.index'
        );
    }

    public function getLogout()
    {
        Auth::logout();
        return Redirect::route('home');
    }

    public function getGithub()
    {
        $github = $this->getGithubOauth();
        $url = $github->getAuthorizationUri();
        return Redirect::to(htmlspecialchars_decode($url));
    }

    public function getGithubResponse()
    {
        $code = Input::get('code');
        $github = $this->getGithubOauth();
        $token = $github->requestAccessToken($code);

        $client = $this->getGithubClient();
        $client->authenticate($token->getAccessToken(), null, \Github\Client::AUTH_HTTP_TOKEN);
        $user = $client->api('current_user')->show();

        if (!$this->validUser($client)) {
            Session::flash('error', 'Access Denied: You must be a member of the Pica9 Github Organization');
            return Redirect::to('login');
        }

        $dbUser = $this->saveUserToken($user, $token);

        Auth::login($dbUser, true);
        return Redirect::route('home');
    }

    protected function validUser($client)
    {
        $orgs = $client->api('current_user')->organizations();
        foreach ($orgs as $org) {
            if ($org['login'] == 'Pica9') {
                return true;
            }
        }
        return false;
    }

    protected function saveUserToken($user, $token)
    {
        //find user, create if not exists
        $userRecord = User::where('github_login', '=', $user['login'])->first();

        if (!$userRecord) {
            $userRecord = new User;
            $userRecord->github_login = $user['login'];
            $userRecord->save();
        }

        //delete previous tokens
        Token::where('user_id', '=', $userRecord->id)->delete();

        //insert new token
        $tokenModel = new Token;
        $tokenModel->access_token = $token->getAccessToken();
        $tokenModel->authorization_server = 'api.github.com';
        $tokenModel->user_id = $userRecord->id;
        $tokenModel->save();

        return $userRecord;
    }

    protected function getGithubOauth($die = false)
    {
        if (!$this->githubOauthService) {
            $serviceFactory = new \OAuth\ServiceFactory();
            $storage = new OAuth\Common\Storage\Session();

            $credentials = new Credentials(
                Config::get('github-api.key'),
                Config::get('github-api.secret'),
                ''
            );
            $scopes = array('repo');
            $this->githubOauthService = $serviceFactory->createService('GitHub', $credentials, $storage, $scopes);
        }
        return $this->githubOauthService;
    }

    protected function getGithubClient()
    {
        $client = new \Github\Client(
            new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
        );
        return $client;
    }

}
