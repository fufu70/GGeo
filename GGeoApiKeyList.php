<?php

class GGeoApiKeyList {
	
	/**
	 * 
	 * default API key (localhost)
	 * @var string API key
	 */
	private $_default = 'ABQIAAAAiNlS-KWUYtfPmXrWytgMmxT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQlQzG8ekt6PEzv6dL5UtfryHSg8g';
	/**
	 * 
	 * Holds the collection of keys
	 * @var CMap
	 */
	private $_keys = null;
	/**
	 * 
	 * Class constructor
	 * @param string $domain
	 * @param string $key
	 */
	public function __construct( $domain=null, $key=null )
	{
		// set default API key
		$this->addAPIKey( 'localhost', $this->_default );
		
		if( $domain != null && $key != null )
			$this->addAPIKey( $domain, $key );
	}
	/**
	 * 
	 * Adds a Google API key to collection
	 * @param string $domain
	 * @param string $key
	 */
	public function addAPIKey( $domain , $key ){
		if( null === $this->_keys ) 
			$this->_keys = new CMap();
		
		$this->_keys->add( $domain, $key );
	}
	/**
	 * 
	 * Returns Google API key if found in collection
	 * @param string $domain
	 * @return string Google API key
	 */
	public function getAPIKeyByDomain( $domain  )
	{
	   if( !$this->_keys->contains( $domain ) )
	       return false;
		return $this->_keys->itemAt( $domain );
	}
	/**
	 * Returns and google api key by domain name discovery
	 * @return Google API key
	 */
	public function guessAPIKey( )
	{
		if (isset($_SERVER['SERVER_NAME']))
	    {
	      return $this->getAPIKeyByDomain( $_SERVER['SERVER_NAME'] );
	    }
	    else if (isset($_SERVER['HTTP_HOST']))
	    {
	      return $this->getAPIKeyByDomain( $_SERVER['HTTP_HOST'] );
	    }
	    return $this->getAPIKeyByDomain('localhost');
  	}
}