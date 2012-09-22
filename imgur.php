<?php 

/**
 * Laravel bundle API wrapper for Imgur anonymous functions
 *
 * @package Imgur
 * @version 1.0
 * @author  Barna Szalai <b.sz@devartpro.com>
 * 
 */

class Imgur 
{
 	private static $imgur_apikey;
	private static $imgur_baseurl = 'http://api.imgur.com/2/';
	private static $imgur_format;
	private static $imgur_xml_type;
	public static $response        = '';

	public static function move_image($params)
	{
		if(self::run(__FUNCTION__, $params))
		{
			return self::$response;
		}

		return FALSE;
	}	

	public static function upload($params = array())
	{
		if(count($params))
		{
			self::$imgur_apikey   = Config::get('imgur::imgur.imgur_apikey');

			// image required
			if( ! array_key_exists('image', $params))
			{
				return FALSE;
			}

			$params = array_merge(array('key' => self::$imgur_apikey), $params);

			if(self::run(__FUNCTION__, $params))
			{
				return self::$response;
			}
		}
		
		return FALSE;		
	}

	public static function stats($param = '')
	{		
		$view_params = array('today', 'week', 'month');
		
		$param = ( ! in_array($param, $view_params) OR ! strlen($param)) ? array() : array('view' => $param);

		if(self::run(__FUNCTION__, $param))
		{			
			return self::$response;
		}

		return FALSE;
	}

	public static function album($id)
	{
		if(self::run(__FUNCTION__, $id))
		{
			return self::$response;
		}

		return FALSE;
	}
	
	public static function image($hash)
	{
		if(self::run(__FUNCTION__, $hash))
		{
			return self::$response;
		}

		return FALSE;
	}

	public static function delete($delete_hash)
	{
		if(self::run(__FUNCTION__, $delete_hash))
		{
			return self::$response;
		}

		return FALSE;
	}

	public static function oembed($params = array())
	{
		if(count($params))
		{
			if( ! array_key_exists('url', $params))
			{
				return FALSE;
			}

			if(self::run(__FUNCTION__, $params))
			{
				return self::$response;
			}
		}	

		return FALSE;
	}	

	public static function run($method, $params)
	{
		$url = '';

		self::$imgur_format   = Config::get('imgur::imgur.imgur_format');
		self::$imgur_xml_type = Config::get('imgur::imgur.imgur_xml_type');		

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		if($method === 'move_image')
		{
			if($params['edit'] === TRUE)
			{
				$url .= self::$imgur_baseurl.'upload?edit&';
				unset($params['edit']);
			}
			else
			{
				$url .= self::$imgur_baseurl.'upload?';	
				unset($params['edit']);
			}

			$url .= http_build_query($params, NULL, '&');

			curl_setopt($ch, CURLOPT_URL, $url);
		}		
		elseif($method === 'upload')
		{
			$url .= self::$imgur_baseurl.$method. '.' .self::$imgur_format;
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);			
		}
		else
		{
			if($method === 'oembed')
			{
				self::$imgur_baseurl = substr(self::$imgur_baseurl, 0, -2);
			}
			// if $params is an array
			if(is_array($params))
			{
				// if there are params
				if(count($params))
				{
					// if format parameter is set, we use it instead of pre-set value
					if(array_key_exists('format', $params))
					{
						$url .= self::$imgur_baseurl.$method.'?';		
					}
					else
					{
						$url .= self::$imgur_baseurl.$method. '.' .self::$imgur_format.'?';		
					}				
				}
				else
				{
					$url .= self::$imgur_baseurl.$method. '.' .self::$imgur_format;	
				}

				$url .= http_build_query($params, NULL, '&');
			}
			// else $params is a string param
			else
			{
				$url .= self::$imgur_baseurl.$method.'/'.$params. '.' .self::$imgur_format;			
			}			

			curl_setopt($ch, CURLOPT_URL, $url);
		}
		
		if( $response = curl_exec($ch))
		{
			switch(self::$imgur_format)
			{
				case 'json' :
					self::$response = $response;
				break;

				case 'xml' :
					// convert string response to xml object or array
					$simplexml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);

					if(self::$imgur_xml_type === 'array')
					{
						self::$response = self::xml_to_array($simplexml);
					}
					else
					{
						self::$response = $simplexml;
					}
				break;
			}			
			curl_close($ch);
			return TRUE;
		}
		else
		{			
			throw new Exception('cURL error: '.curl_error($ch).'. Error code: '.curl_errno($ch));						
			curl_close($ch);
		}		
	}
	
	protected static function xml_to_array($xmlstring)
	{		
		$json = json_encode($xmlstring);
		$array = json_decode($json,TRUE);
	
		return $array;
	}
}