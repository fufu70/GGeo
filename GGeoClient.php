<?php

class GGeoClient
{

    /**
     * The URL for the RESTful geocode API.
     * @since 2011-03-23 Matt Cheale Updated URL from v2 to v3 of the API.
     * @since 2011-04-21 Matt Cheale Removed the format option so it can be customised in the geocoding methods.
     * @since 2011-12-19 Antonio Ramirez renamed to make use of more APIs
     */
    const API_GEOCODE_URL = 'http://maps.googleapis.com/maps/api/geocode/';
    
    /**
     * API key array
     *
     * @var GGeoApiKeyList $api_keys
     */
    protected $api_keys = null;
    /**
     *
     * Holds default domain
     * domains specified active API key
     * @var string
     */
    private $_default_domain = 'localhost';

    /**
     *
     * Constructor
     * If $key parameter is set, it will try to add it
     * to the collection. Array should be in the format of
     * <pre>
     *     $gmapclient = new GGeoClient( array('domain'=>'googlekeyhere') );
     * </pre>
     * @param array $key
     * @since 2011-04-21 Matt Cheale $key parameter deprecated
     * @since 2011-09-18 Antonio Ramirez added $key parameter again
     */
    public function __construct($key = array())
    {
        $this->api_keys = new GGeoApiKeyList();

        if (!empty($key) && !is_scalar($key)) {
            list($domain, $key) = each($key);
            $this->setAPIKey($domain, $key);
        }
    }

    /**
     * Sets the Google Maps API key
     * @param string $domain
     * @param string $key
     * @param bool $setAsDefault
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added $key support again
     */
    public function setAPIKey($domain, $key, $setAsDefault = false)
    {
        if ($this->api_keys === null)
            $this->api_keys = new GGeoApiKeyList();


        $this->api_keys->addAPIKey($domain, $key);

        if (true === $setAsDefault)
            $this->setDomain($domain);
    }

    /**
     *
     * Sets default API key
     * @param string $domain
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added support again
     */
    public function setDomain($domain)
    {
        $this->_default_domain = $domain;

    }

    /**
     * Gets the Google Maps API key
     * @param string $domain the domain
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added support again
     * @return string
     */
    public function getAPIKey($domain = null)
    {
        $domain = (null === $domain ? $this->_default_domain : $domain);
        return $this->api_keys->getAPIKeyByDomain($domain);
    }

    /**
     * Guesses and sets default API Key
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added support again
     */
    protected function guessAndSetAPIKey($key)
    {
        $this->setAPIKey($this->guessDomain(), $key, true);
    }

    /**
     * Guesses the current domain
     * @return string $domain
     * @author Antonio Ramirez Cobos
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added support again
     */
    public function guessDomain()
    {
        if (isset($_SERVER['SERVER_NAME']))
            return $_SERVER['SERVER_NAME'];
        else if (isset($_SERVER['HTTP_HOST']))
            return $_SERVER['HTTP_HOST'];

        // nothing found, return default
        return $this->_default_domain;

    }

    /**
     * Returns the collection of API keys
     * @return GGeoApiKeyList
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added support again
     */
    public function getAPIKeys()
    {
        return $this->api_keys;
    }

    /**
     *
     * Sets the API keys collection
     * @param GGeoApiKeyList $api_keys
     * @return false if $api_keys is not of class CMap
     * @author Antonio Ramirez Cobos
     * @since 2011-04-21 Matt Cheale Deprecated as API Keys are no longer required.
     * @since 2011-09-18 Antonio Ramirez added support again
     */
    public function setAPIKeys($api_keys)
    {
        if (!$api_keys instanceof GGeoApiKeyList)
            return false;

        $this->api_keys = $api_keys;
    }

    /**
     *
     * Changes default geocoding template
     * Just in case google changes its API
     * current is of default: {api}&output={format}&key={key}&q={address}
     * @param string $template
     * @author Antonio Ramirez Cobos
     * @deprecated
     * @since 2011-04-21 Matt Cheale Deprecated as latest code is not making any use of this.
     */
    public function setGeoCodingTemplate($template)
    {
    }

    /**
     *
     * Connection to Google Maps' API web service
     *
     * Modified to include a template for api
     * just in case the url changes in future releases
     * Includes template parsing and CURL calls
     * @author Antonio Ramirez Cobos
     * @since 2010-12-21
     *
     * @param string $address
     * @param string $format 'csv' or 'xml' or 'json'
     * @return string
     * @author fabriceb
     * @since 2009-06-17
     * @since 2010-12-22 cUrl and Yii adaptation Antonio Ramirez
     * @since 2011-04-21 Matt Cheale Updated to API V3 and moved HTTP call to another function.
     *
     */
    public function getGeocodingInfo($address, $format = 'json')
    {
        $apiURL = self::API_GEOCODE_URL . $format . '?address=' . urlencode($address) . '&sensor=false';
        return $this->callApi($apiURL);
    }

    /**
     * Reverse geocoding info
     * @param $lat
     * @param $lng
     * @param string $format
     * @return string
     * @author Vincent Guillon <vincentg@theodo.fr>
     * @since 2010-03-04
     * @since 2010-12-22 modified by Antonio Ramirez (CUrl call)
     * @since 2011-03-23 Matt Cheale Updated the query string to use v3 API variables.
     * @since 2011-04-21 Matt Cheale Added format option and moved HTTP call to another function.
     * @since 2011-12-19 Antonio Ramirez modified API call
     */
    public function getReverseGeocodingInfo($lat, $lng, $format = 'json')
    {
        $apiURL = self::API_GEOCODE_URL . $format . '?latlng=' . $lat . ',' . $lng . '&sensor=false';
        return $this->callApi($apiURL);
    }

    /**
     * Elevation info request
     *
     * @param string $locations the coordinates array to get elevation info from
     * @param string $format 'xml' or 'json'
     * @return string
     * @author Antonio Ramirez
     */
    public function getElevationInfo($locations, $format = 'json')
    {
        $apiURL = self::API_ELEVATION_URL . $format . '?locations=' . $locations . '&sensor=false';
        return $this->callApi($apiURL);
    }

    /**
     * Takes the $apiURL and performs that HTTP request to Google, returning the
     * raw data.
     * @param string $apiUrl
     * @return bool|mixed|string
     * @author Matt Cheale
     * @since 2011-04-21
     * @since 2011-12-17 Modified to fix open_basedir restrictions
     */
    private function callApi($apiUrl)
    {
        if (function_exists('curl_version')) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
            $raw_data = $this->_curl_exec_follow($ch);
            curl_close($ch);
        } else // no CUrl, try differently
        $raw_data = file_get_contents($apiUrl);

        return $raw_data;
    }

    /**
     * This function handles redirections with CURL if safe_mode or open_basedir
     * is enabled.
     * @param resource $ch the curl handle
     * @param integer $maxredirections
     * @return bool|mixed
     * @throws CHttpException
     */
    private function _curl_exec_follow(&$ch, $maxredirections = 5)
    {
        if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $maxredirections > 0);
            curl_setopt($ch, CURLOPT_MAXREDIRS, $maxredirections);
        } else {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            if ($maxredirections > 0) {
                $new_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

                $rch = curl_copy_handle($ch);
                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
                do {
                    curl_setopt($rch, CURLOPT_URL, $new_url);
                    $header = curl_exec($rch);

                    if (curl_errno($rch))
                        $code = 0;
                    else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/', $header, $matches);
                            $new_url = trim(array_pop($matches));
                        } else
                            $code = 0;
                    }
                } while ($code && --$maxredirections);

                curl_close($rch);

                if (!$maxredirections) {
                    if ($maxredirections === null)
                        throw new CHttpException(301, 'Too many redirects. When following redirects, libcurl hit the maximum amount.');
                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $new_url);
            }
        }
        return curl_exec($ch);
    }

}