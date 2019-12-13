<?php
/**
 * Created by PhpStorm.
 * User: francescolotruglio
 * Date: 2019-12-12
 * Time: 23:27
 */

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
            return $response["results"];
        }
        return $response;
    }


    public function getPerson($id)
    {
        $url = self::BASEURL . "people/" . $id;
        $response = $this->requestUrl(self::METHOD_GET, $url);
        if (is_array($response) && isset($response["person"])) {
            return $response["person"];
        }
        return $response;
    }

    public function getPersonContacts($id)
    {
        $url = self::BASEURL . "people/" . $id . "/contacts";
        $response = $this->requestUrl(self::METHOD_GET, $url);
        var_dump($response);
        if (is_array($response) && isset($response["results"])) {
            return $response["results"];
        }
        return $response;
    }

    public function consumePersonAction($edit)
    {
        $isNew = $_POST["action"] === 'edit' ? false : true;
        $_id = isset($_POST["_id"]) ? $_POST["_id"] : false;
        if (empty($_id)) {
            $isNew = true;
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
        $isNew = $action;
        if ($action === "listcontact") {
            return false;
        }
        $_id = isset($_POST["_id"]) ? $_POST["_id"] : false;
        if (empty($_id)) {
            $isNew = true;
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
        $url = self::BASEURL . "sites/cofficegroupdev/pages/events/" . $id;
        $response = $this->requestUrl(self::METHOD_GET, $url);
        if (is_array($response) && isset($response["event"])) {
            return $response["event"];
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
            "status"=> "published"
        ];
        $url = self::BASEURL . "sites/cofficegroupdev/pages/events/" . $_id;
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
}


