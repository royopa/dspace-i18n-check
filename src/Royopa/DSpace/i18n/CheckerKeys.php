<?php

/**
 * Checks the difference messages origin and translated message
 * @author royopa - <royopa@gmail.com>
 */

namespace Royopa\DSpace\i18n;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use GuzzleHttp\Client;
use Doctrine\DBAL\Connection;

class CheckerKeys
{
    public function __construct(Connection $conn, $master, $toCheck)
    {
        $this->conn    = $conn;
        $this->master  = $master;
        $this->toCheck = $toCheck;

        $this->saveFile($toCheck);
        $this->saveFile($master);

        //echo $this->getMessageXmlFile($this->getUrlMessageFile($master));

        //echo $this->getMessageXmlFile($this->getUrlMessageFile($toCheck));

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

    private function getUrlMessageFile($messageFile = 'messages.xml')
    {
        $baseUrl = 'https://raw.githubusercontent.com/DSpace/DSpace/master/dspace-xmlui/src/main/webapp/i18n/';

        if ($messageFile != 'messages.xml') {
            $baseUrl = 'https://raw.githubusercontent.com/DSpace/dspace-xmlui-lang/master/src/main/webapp/i18n/';
        }

        return $baseUrl . $messageFile;
    }

    private function saveFile($messageFile)
    {
        $fromUrl = $this->getUrlMessageFile($messageFile);
        $pathToSave  = __DIR__ . '/../../../../sources/';
        $toFile  = $pathToSave.$messageFile;

        $fp = fopen ($toFile, 'w+');
        $ch = \curl_init();
        curl_setopt( $ch, CURLOPT_URL, $fromUrl );
        curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, false );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );

        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_FILE, $fp );
        curl_exec( $ch );
        curl_close( $ch );
        fclose( $fp );
        
        $this->registerUpdate($messageFile = 'messages.xml');

        return true;
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

    private function registerUpdate($messageFile = 'messages.xml')
    {
        $name_source = 'messages.xml';

        if ($messageFile != 'messages.xml') {
            $name_source = 'translations_messages_xx.xml';
        }

        $this->conn->insert('update_source', array(
          'name_source' => $name_source,
          'date' => date('Y-m-d H:i:s'),
        ));
        
        return true;
    }
}
