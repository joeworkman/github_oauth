# GitHubOAuth PHP Library

This library provides a very simple PHP interface to the GitHub oAuth API v3. Just a few lines of code and you are ready to go!

You need to [register the application](https://github.com/account/applications/new) that you intend to use this library with GitHub. The OAuth keys provided will only work for the one domain that registered. Therefore, if you have multiple domains, you that you would like to run this library on, you will be required to register each domain and obtain a unique set of OAuth keys for each domain. 

I ultimately developed this library becuase I wanted to give access to my github issues of my private repositories to my customers. This way my customers can see what bugs exist and see what I am working on. Take a look at my [Known Issues & Requests](http://joeworkman.net/rapidweaver/issues) page on my website that displays all issues for on of my private repos. you can even drill down into each individual issue and see all of its comments. While all of this is read-only, you could easily add modification and creation abilities using this library. Pretty cool huh!?! 

## Requirements
  * PHP5.1+
  * cURL Extension

## Usage
First you need to load the class…

	require_once('github_oauth.php');

Now we need to create our OAuth object. You will need to make sure that you obtain your OAuth Client ID and Secret key by [registering your app](https://github.com/account/applications/new) with the GitHub. You will also need to specify the scope of the access you are requesting. For more info on scope options, check out the [GitHub OAuth Docs](http://developer.github.com/v3/oauth/).
		
	$oauth = new GitHubOauth($scope, $client_key, $secret_key);

The next block of code is where all the magic happens. This code will load a local cached copy of your OAuth access\_token. If one is not found, it will redirect the user to GitHub for authorization. Once the user authorizes the access, you will be redirected back to the original page where the class will generate the access\_token based off the code supplied by GitHub. 

	if ($oauth->has_access_token() or isset($_GET['code'])) {
		$oauth->get_access_token();
	}
	else {
		$oauth->get_user_authorization();
	}

Now we are all set! We are fully authenticated and can make all the API queries that we want! **Make sure that your requests do not start with a "/"**.

	$issues = json_decode($oauth->request("issues"));



## Functions
This class has eight public functions for you to use:


`set_keys($client_id, $secret)` allows you to set the oauth keys outside of the object creation. Chances are that you won't have to use this method. 


`has_access_token()` allows you to check to see if a cached access token already exists. If one does, then you dont need to bother going through the entire handshake process to get a new one. This method returns TRUE or FALSE. 


`revoke_access_token()` allows you to clear the current access token in the case that you want to obtain a new one.


`get_user_authorization()` starts the handshack process and redirects to GitHub for authorization. Once authorized, GitHub will return to the current page with the *code* parameter. 


`get_access_token()` first checks to see if there is a cached access token. If one exists, it will simply use that instead of requesting a new one from GitHub. If a cached access token does not exist, then this method will request one from GitHub and store is for future use. 


`request($request, $query = array())` allows you to make an HTTP GET request to GitHub. The *$request* argument is a string that contains the GitHub API query that you would like to make. Ensure that the query does not start with a "/". The *$query* argument is and optional array that contains additional API query parameters that you would like to make. These parameters are documented on a per query basis in the GitHub API documentation. 


`post_request($request, $data = array(), $query = array())` allows you to make an HTTP POST request to GitHub. The *$request* argument is a string that contains the GitHub API query that you would like to make. Ensure that the query does not start with a "/". The *$data* argument is an array of the data values that you want to post into GitHub. The *$query* argument is and optional array that contains additional API query parameters that you would like to make. These parameters are documented on a per query basis in the GitHub API documentation. 


`get_current_url()` really has nothing to do with OAuth but is a utility method to obtain the full URI to the current page. The library uses this in order to pass the url that GItHub needs to return to after the handshake. 


## Examples

The `index.php` file included in this repo can serve as a simple example on how to get going.

For a live example, take a look at my [Known Issues & Requests](http://joeworkman.net/rapidweaver/issues) page on my website that displays all issues for on of my private repos. you can even drill down into each individual issue and see all of its comments. While all of this is read-only, you could easily add modification and creation abilities using this library. 

## Download
You can download the latest version, along with all my other libraries by cloning this Git repository stored on GitHub. 