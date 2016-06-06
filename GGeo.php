<?php

class GGeo extends GGeoBase {
	/**
	 * 
	 * default Google Map Options
	 * @var array
	 */
	protected $options = array(
	);

	/**
	 * the interface to the Google Maps API web service
	 */
	protected $gMapClient = null;

	/**
	 * Constructs a Google Map PHP object
	 *
	 * @param array $options Google Map Options
	 * @param array $htmlOptions Container HTML attributes
	 */
	public function __construct($options=array(), $htmlOptions=array())
	{
		$this->gMapClient = new GGeoClient();
	}

	/**
	 * 
	 * Returns the Google API key
	 * @see GGeoClient
	 * @return string $key
	 */
	public function getAPIKey()
	{
		return $this->getGMapClient()->getAPIKey();
	}

	/**
	 * 
	 * Sets a Google API key for a specific domain
	 * @param string $domain
	 * @param string $key
	 */
	public function setAPIKey($domain, $key)
	{
		$this->getGMapClient()->setAPIKey($domain, $key, true);
	}

	/**
	 * Gets an instance of the interface to the Google Map web geocoding service
	 *
	 * @return GGeoClient
	 */
	public function getGMapClient()
	{
		if (null === $this->gMapClient)
			$this->gMapClient = new GGeoClient();

		return $this->gMapClient;
	}

	/**
	 * Sets an instance of the interface to the Google Map web geocoding service
	 *
	 * @param GGeoClient
	 */
	public function setGMapClient($gMapClient)
	{
		$this->gMapClient = $gMapClient;
	}

	/**
	 * Geocodes an address
	 * @param string $address
	 * @return GMapGeocodedAddress
	 * @author Fabrice Bernhard
	 */
	public function geocode($address)
	{
		$address = trim($address);

		$gMapGeocodedAddress = new GGeoAddress($address);
		$accuracy = $gMapGeocodedAddress->geocode($this->getGMapClient());

		if ($accuracy)
			return $gMapGeocodedAddress;

		return null;
	}

	/**
	 * backwards compatibility
	 * @param string[] $api_keys
	 * @return string
	 * @author fabriceb
	 * @since Jun 17, 2009 fabriceb
	 * @since 2010-12-22 modified for Yii Antonio Ramirez
	 */
	public static function guessAPIKey($api_keys = null)
	{
		return GGeoClient::guessAPIKey($api_keys);
	}

}
