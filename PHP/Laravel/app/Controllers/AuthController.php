<!-- ROUTE
Route::post('/oauth', 'AuthController@requestOauth');
-->

<?php

use InoOicClient\Flow\Basic;
use InoOicClient\Http;
use InoOicClient\Client;
use InoOicClient\Oic\Token;

class AuthController extends BaseController {

    private static $authConfig = array(
            'client_info' => array(
                'client_id' => 'CLIENT ID PROPORCIONADO',
                'redirect_uri' => 'http://www.sitio.gob.cl/openid/callback',
                'authorization_endpoint' => 'https://www.claveunica.gob.cl/openid/authorize',
                'token_endpoint' => 'https://www.claveunica.gob.cl/openid/token',
                'user_info_endpoint' => 'https://www.claveunica.gob.cl/openid/userinfo',
                'authentication_info' => array(
                    'method' => 'client_secret_post',
                    'params' => array(
                        'client_secret' => 'CLIENT SECRET PROPORCIONADO'
                    )
                )
            )
        );

	public function getLogin() {
        return View::make('backend/auth/login');
	}

    public function requestOauth() {
        $flow = new Basic(self::$authConfig);

        if (! isset($_GET['redirect'])) {
            try {
                $uri = $flow->getAuthorizationRequestUri('SCOPE');
                return Redirect::to($uri);
            } catch (\Exception $e) {
                printf("Exception during authorization URI creation: [%s] %s", get_class($e), $e->getMessage());
            }
        } else {
            try {
                $userInfo = $flow->process();
            } catch (\Exception $e) {
                printf("Exception during user authentication: [%s] %s", get_class($e), $e->getMessage());
            }
        }

    }

    public function responseOauth() {
        $flow = new Basic(self::$authConfig);
        $token = $flow->getAccessToken($_GET['code']);
        $infoPersonal = $flow->getUserInfo($token);
        $rut = $infoPersonal['RUT'];

        \Auth::login($user);
        return Redirect::to('/usuario/autenticado/con/claveunica');
        
    }

    public function getLogout(){
        Auth::logout();
        return Redirect::to('/logout');
    }

}

