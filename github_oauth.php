<?php
/* GitHubOauth Library v1.0 (http://github.com/joeworkman/github_oauth)
 * Developed by Joe Workman (http://joeworkman.net)
 * Copyright (c) 2011, Joe Workman. All rights reserved.
 * Requires the PHP cURL extension
 */
class GitHubOauth {
	
	public $shared_key = '';
	public $shared_secret = '';
	public $scope = '';
		  
	private $site = 'https://github.com/';
	private $access_token_path = 'login/oauth/access_token';
	private $authorize_path = 'login/oauth/authorize';
	private $token_file = './github_token';
	private $per_page = 100;
	private $access_token;
	private $redirect_uri;
	private $curl;
   
	public function __construct($scope, $key, $secret) {
		$this->scope = $scope;
		$this->set_keys($key, $secret);

		$this->redirect_uri = $this->get_current_url();
		$this->curl = curl_init();     
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE);
	}
   
	// Sets the site consumer key and consumer secret variables
	public function set_keys($key, $secret) {
		$this->shared_key = $key;
		$this->shared_secret = $secret;
	}

	// Check to see if we already have a token saved.
	public function has_access_token() {
		return file_exists($this->token_file) ? TRUE : FALSE;
	}
	// Clear the current access token
	public function revoke_access_token() {
		$this->access_token = null;
		unlink($this->token_file);
		return;
	}
	
      
	// Redirect to GitHub and ask for user authorization. 
	// GitHub should redirect page to the current page with our access code as a page parameter
	public function get_user_authorization() {
		$query = array(
			"client_id" 	=> $this->shared_key,
			"redirect_uri" 	=> $this->redirect_uri,
			"scope" 		=> $this->scope
		);
		$url = $this->site . $this->authorize_path . '?' . http_build_query($query);
		header("Location: ".$url);
	}
   
	// Obtains an access token with the request token passed through.
	public function get_access_token() {

		if ($this->has_access_token()) {
			// If we already have an access token, use it… 
			$this->access_token = $this->get_saved_token();
		}
		else {
			// Request a new access token… 	
			$query = array(
				"client_id" 	=> $this->shared_key,
				"client_secret" => $this->shared_secret,
				"redirect_uri" 	=> $this->redirect_uri,
				"code"      	=> $_GET['code']
			);
			
			$url = $this->site . $this->access_token_path . '?' . http_build_query($query);
			$request = $this->http_post($url, $query);

			$request = explode('&', $request);
			$final = array();
					
			foreach ($request as $item) {
				$a = explode('=', $item);
				$final["$a[0]"] = $a[1];
			}
			$this->access_token = $final['access_token'];
			$this->save_access_token();
		}
		
		return $this->access_token;
	}
   
	// Make an authenticated request to GitHub
	public function request($request, $query = array()) {
		$query['access_token'] = $this->access_token;
		$query['per_page'] = $this->per_page;
		$url = 'https://api.github.com/'.$request . '?' . http_build_query($query); 

		return $this->http_get($url);
	}
	public function post_request($request, $data = array(), $query = array()) {
		$query['access_token'] = $this->access_token;
		$query['per_page'] = $this->per_page;
		$url = 'https://api.github.com/'.$request . '?' . http_build_query($query); 
		
		return $this->http_post($url, $data);
	}

	// Get the current page url
	public function get_current_url() {
	    $page_url = 'http';
	    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$page_url .= "s";}
	    $page_url .= "://";
	    if ($_SERVER["SERVER_PORT"] != "80") {
	        $page_url .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
	    } else {
	        $page_url .= $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];
	    }
	    return $page_url;
	}
   
	//---------------------------------------
	// Private Methods to make HTTP Requests
	//---------------------------------------   	
	private function save_access_token() {
		$handle = fopen($this->token_file, "w");
		fwrite($handle, $this->access_token);
		fclose($handle);
		return;
	}

	private function get_saved_token() {
		return file_get_contents($this->token_file);
	}

	private function http_get($url) {
		curl_setopt($this->curl, CURLOPT_HTTPGET, TRUE);
		return $this->_request($url);
	}
	
	private function http_post($url, $data = array()) {
		curl_setopt($this->curl, CURLOPT_POST, TRUE);
		curl_setopt($this->curl, CURLOPT_POST, count($data));
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));		
		
		return $this->_request($url);
	}
	
	private function _request($url) {
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLINFO_HEADER_OUT, true);
		
		return curl_exec($this->curl);
	}
	
	private function _build_protocol_string($prot) {
		$array = array();
		foreach ($prot as $key => $value) {
			$array[] = "$key=$value";
		}	
		return implode(", ", $array);
	}
	
} // End GitHubOauth Class
?>
