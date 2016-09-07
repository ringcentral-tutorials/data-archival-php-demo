<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;

// ---------------------- Send SMS --------------------
	echo "\n";
	echo "------------Download Call Recordings----------------";
	echo "\n";



try {

	// Writing the call-log response to json file
	$dir = $_ENV['RC_dateFrom'];
    $messageStoreDir = getcwd() . DIRECTORY_SEPARATOR . 'Message-Store/' . $dir;

    //Create the Directory
    if (!file_exists($messageStoreDir)) {
    	mkdir($messageStoreDir, 0777, true);
    }

	$messageStoreList = $platform->get('/account/~/extension/~/message-store', array(
	'messageType' => 'VoiceMail',
	'dateFrom' => '2016-06-22'
	))
	->json()->records;


	$timePerMessageStore = 6;

	foreach ($messageStoreList as $i => $messageStore) {
	
	
	if(property_exists($messageStore,'attachments')) {

		foreach ($messageStore->attachments as $i => $attachment) {

			$id = $attachment->id;
			print "Downloading Message-Store ${id}" . PHP_EOL;

			$uri = $attachment->uri;
			print "Retrieving ${uri}" . PHP_EOL;

		
			$apiResponse = $platform->get($uri);
	    
		    // Retreive the appropriate extension type of the message
		    if($apiResponse->response()->getHeader('Content-Type')[0] == 'application/pdf') {
			    $ext = 'pdf';
		    	$type = 'Fax';	    	
		    }
		    else if($apiResponse->response()->getHeader('Content-Type')[0] == 'audio/mpeg') {
		    	$ext = 'mp3';
		    	$type = 'VoiceMail';	    	
		    }
		    else {
		    	$ext = 'txt';
		    	$type = 'SMS';	    	
		    }

		    $start = microtime(true);
		    file_put_contents("${messageStoreDir}/${type}_${id}.${ext}", $apiResponse->raw());
		    print "Wrote Recording for Call Log Record ${id}" . PHP_EOL;
		    $end=microtime(true);
		    

		    $time = ($end*1000 - $start * 1000);
		    if($time < $timePerMessageStore) {
		    	sleep($timePerMessageStore-$time);
		    }
		}

	}
	
	else{
		print "does not have recording" . PHP_EOL;
	}

}




} catch (HttpException $e) {

    $message = $e->getMessage() . ' (from backend) at URL ' . $e->apiResponse()->request()->getUri()->__toString();
    print 'Expected HTTP Error: ' . $message . PHP_EOL;

}
