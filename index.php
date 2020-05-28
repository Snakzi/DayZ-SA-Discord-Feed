<?php
$payload = trim(file_get_contents("php://input"));
$payload = preg_replace('/:\s*(\-?\d+(\.\d+)?([e|E][\-|\+]\d+)?)/', ': "$1"', $payload);

$webhook = json_decode($payload);

$invoked = $webhook->invoked;
$discordHook = ""; //Discord-WebHook URL
$secret = ""; // WebHook-Secret
$discordName = ""; //Discord-WebHook name

$signature = hash('sha256',  $invoked.":".$secret);

if(strcmp($signature, $webhook->signature) == 0) {
        http_response_code(200);
        echo "OK";
		
		/*
			Send Killmessage to Discord
		*/
		if($webhook->event == "player_kill") {
			$messageContent = ':skull: **' . $webhook->payload->names->murderer . '**' . ' killed ' . '**' . $webhook->payload->names->victim . '**' . ' with ' . '**' . $webhook->payload->weapon . '**' . ' (' . $webhook->payload->distance . 'm)';
			postToDiscord($messageContent);
		}
        return "OK";
} else {
        echo "BAD";
        return "BAD";
}

function postToDiscord($message)
{
  $json_data = json_encode(["content" => $message, "username" => $discordName]);
	$ch = curl_init($discordHook);
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
	curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt( $ch, CURLOPT_HEADER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
	echo curl_exec( $ch );
	curl_close( $ch );
}
?>
