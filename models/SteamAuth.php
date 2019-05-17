<?php namespace Marin\SteamAuth\Models;

use Exception;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Fluent;

/**
 * SteamAuth Model
 */
class SteamAuth
{

    /**
     * @var integer|null
     */
    public $steamId = null;

    /**
     * @var array|null
     */
    public $steamInfo = null;

    /**
     * @var string
     */
    public $authUrl;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    const OPENID_URL = 'https://steamcommunity.com/openid/login';

    /**
     * @var string
     */
    const STEAM_INFO_URL = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s';

    /**
     * @var string
     */
    const STEAM_REDIRECT = '/marin/steamauth/response';

    /**
     * Create a new SteamAuth instance
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->authUrl = $this->buildUrl(url(self::STEAM_REDIRECT, [],
            SteamConfig::get('steam-auth.https')));
        $this->guzzleClient  = new GuzzleClient;
    }

    /**
     * Validates if the request object has required stream attributes.
     *
     * @return bool
     */
    private function requestIsValid()
    {
        return $this->request->has('openid_assoc_handle')
        && $this->request->has('openid_signed')
        && $this->request->has('openid_sig');
    }

    /**
     * Checks the steam login
     *
     * @return bool
     */
    public function validate()
    {
        if (!$this->requestIsValid()) {
            return false;
        }

        $params = $this->getParams();

        $response = $this->guzzleClient->request('POST', self::OPENID_URL, [
            'form_params' => $params
        ]);

        $this->parseResults($response->getBody()->getContents());
        $this->parseSteamID();
        $this->parseInfo();

        return $this->getUserInfo() != null;
    }

    /**
     * Get param list for openId validation
     *
     * @return array
     */
    public function getParams()
    {
        $params = [
            'openid.assoc_handle' => $this->request->get('openid_assoc_handle'),
            'openid.signed'       => $this->request->get('openid_signed'),
            'openid.sig'          => $this->request->get('openid_sig'),
            'openid.ns'           => 'http://specs.openid.net/auth/2.0',
            'openid.mode'         => 'check_authentication'
        ];

        $signedParams = explode(',', $this->request->get('openid_signed'));

        foreach ($signedParams as $item) {
            $value = $this->request->get('openid_' . str_replace('.', '_', $item));
            $params['openid.' . $item] = get_magic_quotes_gpc() ? stripslashes($value) : $value;
        }

        return $params;
    }

    /**
     * Parse openID reponse to fluent object
     *
     * @param  string $results openid reponse body
     * @return Fluent
     */
    public function parseResults($results)
    {
        $parsed = [];
        $lines = explode("\n", $results);

        foreach ($lines as $line) {
            if (empty($line)) continue;

            $line = explode(':', $line, 2);
            $parsed[$line[0]] = $line[1];
        }

        return new Fluent($parsed);
    }

    /**
     * Validates a given URL, ensuring it contains the http or https URI Scheme
     *
     * @param string $url
     *
     * @return bool
     */
    private function validateUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return true;
    }

    /**
     * Build the Steam login URL
     *
     * @param string $return A custom return to URL
     * @return string
     * @throws Exception
     */
    private function buildUrl($return = null)
    {
        if (is_null($return)) {
            $return = url('/', [], SteamConfig::get('use_https'));
        }
        if (!is_null($return) && !$this->validateUrl($return)) {
            throw new Exception('The return URL must be a valid URL with a URI Scheme or http or https.');
        }

        $params = array(
            'openid.ns'         => 'http://specs.openid.net/auth/2.0',
            'openid.mode'       => 'checkid_setup',
            'openid.return_to'  => $return,
            'openid.realm'      => (SteamConfig::get('use_https') ? 'https' : 'http') . '://' . $this->request->server('HTTP_HOST'),
            'openid.identity'   => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        );

        return self::OPENID_URL . '?' . http_build_query($params, '', '&');
    }


    /**
     * Parse the steamID from the OpenID response
     *
     * @return void
     */
    public function parseSteamID()
    {
        preg_match("#^https://steamcommunity.com/openid/id/([0-9]{17,25})#", $this->request->get('openid_claimed_id'), $matches);
        $this->steamId = is_numeric($matches[1]) ? $matches[1] : 0;
    }

    /**
     * Get user data from steam api
     *
     * @return void
     */
    public function parseInfo()
    {
        if (is_null($this->steamId)) return;

        try{
            $reponse = $this->guzzleClient->request('GET', sprintf(self::STEAM_INFO_URL, SteamConfig::get('api_key'), $this->steamId));
            $json = json_decode($reponse->getBody(), true);
            $this->steamInfo = $json["response"]["players"][0];

        }catch(Exception $e) {
            $this->steamInfo = null;
            $this->error = "Access is denied. Please verify your API key";
        }
    }

    /**
     * Returns the login url
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->authUrl;
    }

    /**
     * Returns the SteamUser info
     *
     * @return array
     */
    public function getUserInfo()
    {
        return $this->steamInfo;
    }

    /**
     * Returns the steam id
     *
     * @return bool|string
     */
    public function getSteamId()
    {
        return $this->steamId;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }



}
