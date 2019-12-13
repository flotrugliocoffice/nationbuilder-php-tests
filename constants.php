<?php
/**
 * Created by PhpStorm.
 * User: francescolotruglio
 * Date: 2019-12-13
 * Time: 01:41
 */
const CLIENT_ID = 'e9b942f8585831369a0a09180d8542872e1ad449ccf86f057219a12d2145f10d';
const CLIENT_SECRET = '956703808cd77fbe7da2aabc3f85eaae1151d93615a50364998ef7eaf7eee5ac';
const REDIRECT_URI = 'https://nb.cofficegroup.net';
const AUTHORIZATION_ENDPOINT = 'https://cofficegroupdev.nationbuilder.com//oauth/authorize';
const TOKEN_ENDPOINT = 'https://cofficegroupdev.nationbuilder.com//oauth/token';
const RESOURCE_ENDPOINT = 'https://cofficegroupdev.nationbuilder.com//oauth/resource';
const SITE_SLUG = 'cofficegroupdev';
if (!defined("SITE_SLUG")) {
    define("SITE_SLUG", SITE_SLUG);
}
