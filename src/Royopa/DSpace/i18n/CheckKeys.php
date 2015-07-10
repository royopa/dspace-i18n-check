<?php

/**
 * Checks the difference messages origin and translated message
 * @author royopa - <royopa@gmail.com>
 */

namespace Royopa\DSpace\i18n;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use GuzzleHttp\Client;

class CheckKeys
{
    public function __construct($master, $toCheck)
    {
        //echo $this->getMessageXmlFile($this->getUrlMessageFile($master));

        echo $this->getMessageXmlFile($this->getUrlMessageFile($toCheck));

        //$argc = 3;

        //if ($argc != 3) {
            //echo "Usage: checkkeys.php <master> <tocheck>\n";
            //exit(1);
        //}
         
        //$masterKeys  = $this->getKeys($argv[1]);
        //$toCheckKeys = $this->getKeys($argv[2]);
         
        //print "IN $argv[1] BUT NOT IN $argv[2]:\n\n";
        //$this->printMissing($masterKeys, $toCheckKeys);
         
        //print "\n\n\nIN $argv[2] BUT NOT IN $argv[1]:\n\n";
        //$this->printMissing($toCheckKeys, $masterKeys);
    }

    private function getUrlMessageFile($message = 'messages.xml')
    {
        $baseUrl = 'https://raw.githubusercontent.com/DSpace/DSpace/master/dspace-xmlui/src/main/webapp/i18n/';

        if ($message != 'messages.xml') {
            $baseUrl = 'https://raw.githubusercontent.com/DSpace/dspace-xmlui-lang/master/src/main/webapp/i18n/';
        }

        return $baseUrl . $message;
    }

    private function getMessageXmlFile($url)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        return $response->getBody();
    }

    private function printMissing($reference, $test)
    {
        foreach ($test as $value) {
            if (! in_array($value, $reference)) {
                echo "$value \n";
            }
        }
    }
     
    private function readFileMessage($path)
    {
        if (! $fileContent = @file($path)) {
            echo "Can't open $path \n";
            exit(1);
        }
     
        return $fileContent;
    }
     
    private function getKeys($path)
    {
        $fileContent = readFileMessage($path);
     
        return readKeys($fileContent);
    }
     
    private function readKeys($file)
    {
        $keys = [];
     
        foreach ($file as $key => $line) {
            if (strpos($line, '<message key="') === false) {
                continue;
            }
     
            $key = getKey($line);
     
            $keys[] = $key;
        }
     
        return $keys;
    }
     
    private function getKey($line)
    {
        $line = trim($line);
     
        $line = substr($line, 14);
     
        $charEnd = strpos($line, '">');
     
        $key = substr($line, 0, $charEnd - strlen($line));
     
        return $key;
    }
}
