<?php
/**
 * @Name GW2 API Test
 * @Author Flutterderp
 * @Version 0.5.0
 * 
 * @Todo
 */
date_default_timezone_set('UTC');

class GW2
{
	protected $api_key;
	//protected $user_name;
	protected $base_url;
	protected $ch;
	protected $base_dir;
	protected $cache_time;
	
	function __construct($authkey = '')
	{
		$this->base_dir		= __DIR__ . '/json_files/';
		$this->base_url 	= 'https://api.guildwars2.com/v2/';
		$this->cache_time	= 5 * 60;
		$this->ch					= curl_init();
		$headers					= array();
		//$headers[]				= 'Content-length: 0';
		$headers[]				= 'Content-type: application/json';
		if($authkey)
		{
			$this->api_key	= $authkey;
			$headers[]			= 'Authorization: Bearer ' . $this->api_key;
		}
		
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->ch, CURLOPT_HEADER, false);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
	}

	function __destruct()
	{
		curl_close($this->ch);
	}
	
	function accountDetails()
	{
		$cache_file		= $this->base_dir . 'account_' . sha1($this->api_key) . '.json';
		$fetch_cache	= @file_get_contents($cache_file);
		
		if(($fetch_cache !== false) && ((filemtime($cache_file) + $this->cache_time) > time()))
		{
			// Use our cached results
			$response = $fetch_cache;
			$return		= json_decode($response, true);
		}
		else
		{
			// Write to a JSON file
			curl_setopt($this->ch, CURLOPT_URL, $this->base_url . 'account?lang=en');
			$response	= curl_exec($this->ch);
			$return		= json_decode($response, true);
			
			// Fetch name of world first…
			if(isset($return['world']))
			{
				curl_setopt($this->ch, CURLOPT_URL, $this->base_url . 'worlds?id=' . $return['world'] . '&lang=en');
				$response								= curl_exec($this->ch);
				$return['world_name']		= json_decode($response, true)['name'];
			}
			
			@file_put_contents($cache_file, json_encode($return));
			@touch($cache_file, time());
		}
		
		return $return;
	}
	
	function getCharacters()
	{
		$cache_file		= $this->base_dir . 'chars_' . sha1($this->api_key) . '.json';
		$fetch_cache	= @file_get_contents($cache_file);
		
		if(($fetch_cache !== false) && ((filemtime($cache_file) + $this->cache_time) > time()))
		{
			// Use our cached results
			$response = $fetch_cache;
		}
		else
		{
			// Write to a JSON file
			curl_setopt($this->ch, CURLOPT_URL, $this->base_url . 'characters?lang=en');
			$response = curl_exec($this->ch);
			@file_put_contents($cache_file, $response);
			@touch($cache_file, time());
		}	
		
		return json_decode($response, true);
	}
	
	function charInfo($char_name = '')
	{
		$char_name		= rawurlencode($char_name);
		$cache_file		= $this->base_dir . $char_name . '.json';
		$fetch_cache	= @file_get_contents($cache_file);
		
		if(($fetch_cache !== false) && ((filemtime($cache_file) + $this->cache_time) > time()))
		{
			// Use our cached results
			$response = $fetch_cache;
		}
		else
		{
			// Write to a JSON file
			curl_setopt($this->ch, CURLOPT_URL, $this->base_url . 'characters/' . $char_name . '?lang=en');
			$response = curl_exec($this->ch);
			@file_put_contents($cache_file, $response);
			@touch($cache_file, time());
		}
		
		return json_decode($response, true);
	}
	
	// Old stuff past this comment :(
	/*function getWorldList()
	{
		curl_setopt($this->ch, CURLOPT_URL, $this->base_url . 'world_names.json?lang=en');
		$response = curl_exec($this->ch);
		$response = json_decode($response);
		//curl_close($this->ch);
		
		$worlds = array();
		foreach($response as $world)
		{
			$worlds[$world->id] = $world->name;
		}
		
		natcasesort($worlds);
		return $worlds;
	}
	
	function getEvent($event_id = '')
	{
		//curl_setopt($this->ch, CURLOPT_URL, $this->baseurl . 'event_details.json?event_id=' . $event_id);
	}
	
	function getEvents($world_id = '1011')
	{
		curl_setopt($this->ch, CURLOPT_URL, $this->base_url . 'events.json?world_id=' . $world_id);
		$response	= curl_exec($this->ch);
		$events		= json_decode($response);
		//curl_close($this->ch);
		return $events;
	}*/
}
