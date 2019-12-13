<?php
/**
 * Created by PhpStorm.
 * User: francescolotruglio
 * Date: 2019-12-12
 * Time: 22:43
 */
require_once 'vendor/autoload.php';
require_once 'authClass.php';

use Jenssegers\Blade\Blade;

require_once 'constants.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
global $AuthSingleton;
$AuthSingleton = authClass::getInstance();

function authenthicateMe()
{
    global $AuthSingleton;
    $provider = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId' => CLIENT_ID,
        'clientSecret' => CLIENT_SECRET,
        'redirectUri' => REDIRECT_URI,
        'urlAuthorize' => AUTHORIZATION_ENDPOINT,
        'urlAccessToken' => TOKEN_ENDPOINT,
        'urlResourceOwnerDetails' => RESOURCE_ENDPOINT
    ]);

    if (isset($_SESSION['oauth2token'])) {
        if (empty($_SESSION['oauth2token'])) {
            unset($_SESSION['oauth2token']);
        }
        $AuthSingleton->setToken($_SESSION['oauth2token'], $provider);
        $blade = new Blade('resources/views', 'cache');
        echo $blade->make('homepage', ['title' => 'Welcome App tester', 'auth' => $AuthSingleton])->render();
        return;
    }

    if (!isset($_GET['code'])) {
        $AuthSingleton->invalidateToken();
        $authorizationUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        header('Location: ' . $authorizationUrl);
        exit;
    } elseif (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {
        if (isset($_SESSION['oauth2state'])) {
            unset($_SESSION['oauth2state']);
        }
        exit('Invalid state');
    } else {
        $code = $_GET["code"];
        $status = $_GET["state"];
        try {
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code
            ]);
            $_SESSION['oauth2token'] = $accessToken->getToken();
            $AuthSingleton->setToken($accessToken->getToken(), $provider);
            $blade = new Blade('resources/views', 'cache');
            echo $blade->make('homepage', ['title' => 'Welcome App tester', 'auth' => $AuthSingleton])->render();

        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            exit($e->getMessage());
        }
    }
}


authenthicateMe();





