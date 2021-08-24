<?php

$client_id = 'dj0yJmk9RlRaZzFSSGhRdlpCJmQ9WVdrOVkzVmxWRlkyVGpRbWNHbzlNQT09JnM9Y29uc3VtZXJzZWNyZXQmc3Y9MCZ4PTk1';
$client_secret = 'e44a7e46a3923b2dae87c8b7e54a3fcfcf387d56';

$method = 'GET';
$game_code = 'nfl';
$url = 'https://fantasysports.yahooapis.com/fantasy/v2/games;game_keys=' . $game_code  . '/players;count=25';

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

$base_string = urlencode($method) . '&' . urlencode($url) . '&' .
  urlencode($params);

$secret = urlencode($client_secret) . '&' . urlencode($access_token_secret);
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
            $player_data = array('position' => $player->display_position,
                                 'name' => $player->name->full);
            $fantasy_players[] = $player_data;
        }
    }
}

$num_players = count($fantasy_players);

print "\n";
if ($num_players == 0) {
    print "Keine Fantasy Player gefunden für: ${game_code}.";
	print "<br>";
} else {
    print "<h1>Fantasy Player Liste</h1>";
	print "<h3>Anzahl Player: ${num_players}</h3>";
	print "<h3>Für: ${game_code}</h3>";
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
}

?>