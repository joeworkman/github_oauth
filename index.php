<?php 
require_once('github_oauth.php');

$client_key = 'insert_api_client_id'; // Insert you Client ID from your registered GitHub API 
$secret_key = 'insert_api_secret_key'; // Insert you Secret Key from your registered GitHub API 
$scope		= 'repo'; // Enter the scope that you want. Go to http://developer.github.com/v3/oauth for more info on scope.

// Create our OAuth object
$oauth = new GitHubOauth($scope, $client_key, $secret_key);

// If we already have an access token or the get parameter is specified to get a new one, get on.
// Else we need to authenticate the user.
($oauth->has_access_token() or isset($_GET['code'])) ? $oauth->get_access_token() : $oauth->get_user_authorization();

// Submit your request. See http://developer.github.com/v3 for more details on possible queries
$issues = json_decode($oauth->request("issues"));
?>

<html>
<body>

<h1>My Github Issues</h1>
<ul>
	<?php foreach($issues as $issue) { echo "<li>#$issue->number - $issue->title</li>"; } ?>
</ul>

</body>
</html>
