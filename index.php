<!doctype html>
<html>
  
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title></title>
		<style type="text/css">
			table {
				margin: 8px;
			}
		</style>
	</head>
  
<body>

<?php

	$client_id = '';
	$client_secret = '';

	$method = 'GET';
	$url = 'https://fantasysports.yahooapis.com/fantasy/v2/games;game_keys=nfl/players;count=25';

	// OAuth
	$oauth_consumer_key = $client_id;
	$oauth_nonce = rand(0, 1000000);
	$oauth_signature_method = 'HMAC-SHA1';
	$oauth_timestamp = time();
	$oauth_version = '1.0';

	$params =  '';
	$params .= 'oauth_consumer_key=' . urlencode($oauth_consumer_key);
	$params .= '&oauth_nonce=' . urlencode($oauth_nonce);
	$params .= '&oauth_signature_method=' . urlencode($oauth_signature_method);
	$params .= '&oauth_timestamp=' . urlencode($oauth_timestamp);
	$params .= '&oauth_version=' . urlencode($oauth_version);

	$base_string = urlencode($method) . '&' . urlencode($url) . '&' . urlencode($params);

	$secret = urlencode($client_secret) . '&' . urlencode();
	$signature = base64_encode(hash_hmac('sha1', $base_string, $secret, true));

	$final_url = $url . '?' . $params . '&oauth_signature=' . urlencode($signature);

	// request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $final_url);
	curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	$timeout = 2;
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

	$result = curl_exec($ch);
	$ret_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	// NFL players
	$fantasy_response = new SimpleXMLElement($result);

	$fantasy_players = array();
	foreach ($fantasy_response->games->game as $game) {
		foreach ($game->players->player as $player) {
			if ($player->player_key) {
				$player_data = array('position' => $player->display_position, 'name' => $player->name->full);
				$fantasy_players[] = $player_data;
			}
		}
	}

	$num_players = count($fantasy_players);
	$player_rank_number = 0;

	print "\n";
	print "<h1>NFL Fantasy Player Liste</h1>";
	print "<br>";
	
    foreach ($fantasy_players as $player_data) {
        $display_position = $player_data['position'];
        $player_name = $player_data['name'];
		$player_rank_number = $player_rank_number + 1;
		
		if ($player_rank_number < 10) {
			print "$player_rank_number."; 
			print "&nbsp;&nbsp;&nbsp;&nbsp;";
			print "${player_name} (${display_position})";
		} else {
			print "$player_rank_number.";
			print "&nbsp;&nbsp;";
			print "${player_name} (${display_position})";
		}
		print "<br>";
    }	
?>

</body>

</html>
