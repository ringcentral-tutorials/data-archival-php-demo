<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK;


require('vendor/autoload.php');

date_default_timezone_set ('UTC');


// To parse the .env file
$dotenv = new Dotenv\Dotenv(getcwd());

$dotenv->load();

// Retreive .env variables
$skipCallLog = $_ENV['RC_SkipCallLog'];
$skipDownload = $_ENV['RC_SkipDownload'];
$skipDownloadS3 = $_ENV['RC_SkipDownloadS3'];
$skipDownloadDropbox = $_ENV['RC_SkipDownloadDropbox'];
$skipMessageStore = $_ENV['RC_SkipMessageStore'];


	// To authenticate
	require(__DIR__ . '/demo/authData.php');


	// Call-Logs
	if ($skipCallLog!="True" || $skipCallLog=="") {
		print "Test 1: callLog.php\n";
	    require(__DIR__ . '/demo/callLog.php');
	} else {
		print "Test 1: call_log.php - skipping...\n";
	}

	// Recordings-Download
	if ($skipDownload!="True" || $skipDownload=="") {
		print "Test 2: callRecording.php\n";
		print "Downloading Recordings\n";
	    require(__DIR__ . '/demo/callRecording.php');
	} else {
		print "Test 2: callRecording.php - skipping...\n";
	}

	// Recordings-Download-Amazon-S3
	if ($skipDownloadS3!="True" || $skipDownloadS3=="") {
		print "Test 3: callRecording_S3.php\n";
		print "Downloading Recordings to S3\n";
	    require(__DIR__ . '/demo/callRecording_S3.php');
	} else {
		print "Test 3: callRecording_S3.php - skipping...\n";
	}

	// Recordings-Download-DropBox
	if ($skipDownloadDropbox!="True" || $skipDownloadDropbox=="") {
		print "Test 4: callRecording_Dropbox.php\n";
		print "Downloading Recordings to Dropbox\n";
	    require(__DIR__ . '/demo/callRecording_Dropbox.php');
	} else {
		print "Test 4: callRecording_Dropbox.php - skipping...\n";
	}

	// Message-store
	if ($skipMessageStore!="True" || $skipMessageStore=="") {
		print "Test 5: messageStore.php\n";
		print "Downloading Message-store List\n";
	    require(__DIR__ . '/demo/messageStore.php');
	} else {
		print "Test 5: messageStore.php - skipping...\n";
	}