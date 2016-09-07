<?php

use RingCentral\SDK\Http\HttpException;
use RingCentral\http\Response;
use RingCentral\SDK\SDK;
date_default_timezone_set ('UTC');

echo "\n";
echo "------------Get Call Logs----------------";
echo "\n";


try {

        // To parse the .env
        $dotenv = new Dotenv\Dotenv(getcwd());

        $dotenv->load();

        // constants
            $pageCount = 1;
            $recordCountPerPage = 100;
            $timePerCallLogRequest = 10;
            $flag = True;

        // Export call-log response to json file
        $dir = $_ENV['RC_dateFrom'];
        $callLogDir = getcwd() . '/Call-Logs/' . $dir;

        //Create the Directory
        if (!file_exists($callLogDir)) {
            mkdir($callLogDir, 0777, true);
          }

        // dateFrom and dateTo paramteres
        $timeFrom = '00:00:00';
        $timeTo = '00:59:59';

        // Array to push the call-logs to a file
        $callLogs = array();

        while($flag) {

            // Start Time
            $start = microtime(true);
            $dateFrom = $_ENV['RC_dateFrom'] . 'T' . $timeFrom;

            $dateTo = $_ENV['RC_dateTo'] . 'T' . $timeTo;

            $apiResponse = $platform->get('/account/~/extension/~/call-log', array(
            'dateFrom' => $dateFrom,
            'withRecording' => 'True',
            // 'dateTo' => $dateTo,
            'type' => 'Voice',
            'perPage' => 10,
            'page' => $pageCount
            ));

            $apiResponseArray = $apiResponse->json()->records;

            $apiResponse->json()->records;

            foreach ($apiResponseArray as $value) {
                array_push($callLogs, $value);
            }

            $end=microtime(true);

            print 'Page ' . $pageCount . 'retreived with ' . $recordCountPerPage . 'records' . PHP_EOL;

            // Check if the recording completed wihtin 10 seconds.
                $time = ($end*1000 - $start*1000) / 1000;

            // Check if 'nextPage' exists
            if(isset($apiResponseJSONArray["navigation"]["nextPage"])) {

                if($time < $timePerCallLogRequest) {
                    print 'Sleeping for :' . $timePerCallLogRequest - $time . PHP_EOL;
                    sleep($timePerCallLogRequest-$time);
                    sleep(5);
                    $pageCount = $pageCount + 1;
                }
            }

            else {

                    print_r ($callLogs);
                    file_put_contents("${callLogDir}/call_log_${'dir'}.json", json_encode($callLogs));
                    $flag = False;
                    unset($callLogs);
            }
        }

} catch (HttpException $e) {

            $message = $e->getMessage();

            print 'Expected HTTP Error: ' . $message . PHP_EOL;

            $apiResponse = $e->apiResponse();
            print 'The Request is :' . PHP_EOL;
            print_r($apiResponse->request());
            print PHP_EOL;
            print 'The Response is :' . PHP_EOL;
            print_r($apiResponse->response());
            print PHP_EOL;

            // Another way to get message, but keep in mind, that there could be no response if request has failed completely
            print '  Message: ' . $e->apiResponse->response()->error() . PHP_EOL;

}
