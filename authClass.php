<?php
/**
 * Created by PhpStorm.
 * User: francescolotruglio
 * Date: 2019-12-12
 * Time: 23:27
 */

use Carbon\Carbon;

class authClass
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DESTROY = 'DELETE';
    const METHOD_PUT = 'PUT';
    const BASEURL = "https://cofficegroupdev.nationbuilder.com/api/v1/";
    protected $isauthenticaed = false;
    protected $token = false;
    protected $meId = false;
    protected $me = false;
    protected $provider = null;

    public function setToken($tk, $provider)
    {
        $this->token = $tk;
        $this->isauthenticaed = true;
        $this->provider = $provider;
        $url = self::BASEURL . "people/me";
        $me = $this->requestUrl(self::METHOD_GET, $url);
        if (!empty($me) && is_array($me) && isset($me["person"])) {
            $this->meId = $me["person"]["id"];
            $this->me = $me["person"];
        }
    }

    public function getToken()
    {
        return $this->token;
    }

    public function invalidateToken()
    {
        $this->token = "";
        $this->isauthenticaed = false;
    }

    private function requestUrl($method, $url)
    {
        if (!$this->isauthenticaed) {
            throw new Exception("ERROR (not authenticated)");
        }
        if (empty($this->provider)) {
            throw new Exception("ERROR (no provider)");
        }
        $options = [
            "headers" => [
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ]
        ];
        $request = $this->provider->getAuthenticatedRequest($method, $url, $this->token, $options);
        return $this->provider->getParsedResponse($request);
    }

    private function postUrl($method, $url, $body)
    {
        if (!$this->isauthenticaed) {
            throw new Exception("ERROR (not authenticated)");
        }
        if (empty($this->provider)) {
            throw new Exception("ERROR (no provider)");
        }
        $options = [
            "headers" => [
                "Content-Type" => "application/json",
                "Accept" => "application/json",
            ],
            "body" => json_encode($body)
        ];
        $request = $this->provider->getAuthenticatedRequest($method, $url, $this->token, $options);
        return $this->provider->getParsedResponse($request);
    }

    public function getPeople()
    {
        $url = self::BASEURL . "people/?limit=10";
        $response = $this->requestUrl(self::METHOD_GET, $url);
        if (is_array($response) && isset($response["results"])) {
            return $this->normalizeArrayDates($response["results"]);
        }
        return $response;
    }


    public function getPerson($id)
    {
        $url = self::BASEURL . "people/" . $id;
        $response = $this->requestUrl(self::METHOD_GET, $url);
        if (is_array($response) && isset($response["person"])) {
            return $this->normalizeArrayDates([$response["person"]])[0];
        }
        return $response;
    }

    public function getPersonContacts($id)
    {
        $url = self::BASEURL . "people/" . $id . "/contacts";
        $response = $this->requestUrl(self::METHOD_GET, $url);
        if (is_array($response) && isset($response["results"])) {
            return $this->normalizeArrayDates($response["results"]);
        }
        return $this->normalizeArrayDates($response);
    }

    public function consumePersonAction($edit)
    {
        $_id = isset($_POST["_id"]) ? $_POST["_id"] : false;
        if (empty($_id)) {
            $_id = false;
        }

        if ($_POST["action"] === 'delete') {
            $url = self::BASEURL . "people";
            $method = self::METHOD_DESTROY;
            if ($_id) {
                $url .= "/" . $_id;
                $method = self::METHOD_DESTROY;
            }
            return $this->requestUrl($method, $url);
        }
        $params = [
            "email" => $_POST["email"],
            "first_name" => $_POST["first_name"],
            "last_name" => $_POST["last_name"]
        ];
        $url = self::BASEURL . "people";
        $method = self::METHOD_POST;
        if ($_id) {
            $url .= "/" . $_id;
            $method = self::METHOD_PUT;
        }
        return $this->postUrl($method, $url, ["person" => $params]);


    }

    public function consumePersonContactAction($id, $action)
    {
        if ($action === "listcontact") {
            return false;
        }
        $_id = isset($_POST["_id"]) ? $_POST["_id"] : false;
        if (empty($_id)) {
            $_id = false;
        }


        $params = [
            "method" => $_POST["method"],
            "note" => $_POST["note"],
            "status" => 'left_message',
            'sender_id' => $this->meId
        ];
        $url = self::BASEURL . "people";
        $url .= "/" . $_id . "/contacts";
        $method = self::METHOD_POST;
        return $this->postUrl($method, $url, ["contact" => $params]);
    }

    public function getEvent($id)
    {
        //GET /sites/:site_slug/pages/events/:id
        $url = self::BASEURL . "sites/" . SITE_SLUG . "/pages/events/" . $id;
        $response = $this->requestUrl(self::METHOD_GET, $url);
        if (is_array($response) && isset($response["event"])) {
            return $this->normalizeArrayDates([$response["event"]])[0];
        }
        return $response;
    }

    public function consumeEventAction($act)
    {
        $_id = isset($_POST["_id"]) ? $_POST["_id"] : false;
        if (empty($_id)) {
            $_id = false;
        }


        $params = [
            "name" => $_POST["name"],
            "headline" => $_POST["headline"],
            "title" => $_POST["title"],
            "excerpt" => $_POST["excerpt"],
            "start_time" => $_POST["start_time"],
            "end_time" => $_POST["end_time"],
            "status" => "published"
        ];
        $url = self::BASEURL . "sites/" . SITE_SLUG . "/pages/events/" . $_id;
        $method = self::METHOD_POST;
        if ($_id) {
            $method = self::METHOD_PUT;
        }
        return $this->postUrl($method, $url, ["event" => $params]);
    }

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    private function normalizeArrayDates($array)
    {
        if (empty($array)) {
            return [];
        }
        if (count($array) === 0) {
            return [];
        }
        static $dateFields = ["start_time", "end_time", "start_date", "end_date", "created_at", "published_at", "updated_at"];
        foreach ($array as &$item) {
            if (is_array($item)) {
                $item = $this->normalizeArrayItem($item, $dateFields);
            } else if (is_object($item)) {
                $item = $this->normalizeObjectItem($item, $dateFields);
            }
        }
        return $array;

    }

    private function normalizeArrayItem($item, $datefields)
    {
        foreach ($item as $key => $value) {
            if (in_array($key, $datefields)) {
                $item[$key] = new Carbon($value);
            }
        }
        return $item;
    }

    private function normalizeObjectItem($item, $datefields)
    {
        $extractProperties = get_object_vars($item);
        foreach ($extractProperties as $key) {
            if (in_array($key, $datefields)) {
                $item->{$key} = new Carbon($item->{$key});
            }
        }
        return $item;
    }

    public function formatDate($object, $format = "Y-m-d")
    {
        if (is_a($object, "Carbon\Carbon")) {
            /* @var Carbon $object */
            return $object->format($format);
        }

        return $object;
    }
}


