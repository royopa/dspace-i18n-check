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
use Royopa\DSpace\i18n\Reader;

class CheckerKeys
{
    private $inMasterNotInToCheck = array();

    private $inToCheckNotInMaster = array();

    public function __construct(Connection $conn, $master, $toCheck)
    {
        $this->conn    = $conn;
        $this->master  = $master;
        $this->toCheck = $toCheck;
        $this->pathToSave = __DIR__ . '/../../../../sources/';
        $this->fullPathMaster  = $this->pathToSave.$master;
        $this->fullPathToCheck = $this->pathToSave.$toCheck;

        $fs = new Filesystem();
        if (! $fs->exists($this->fullPathMaster)) {
            $this->saveFile($master);
        }

        if (! $fs->exists($this->fullPathToCheck)) {
            $this->saveFile($toCheck);
        }

        $reader = new Reader();

        $masterArrayKeys  = $reader->getArrayKeysAndValues($this->fullPathMaster);
        $toCheckArrayKeys = $reader->getArrayKeysAndValues($this->fullPathToCheck);

        //in master but not in translation file
        $this->inMasterNotInToCheck = $this
            ->buildMissingKeys($masterArrayKeys, $toCheckArrayKeys);
        
        //in translation but not in master
        $this->inToCheckNotInMaster = $this
            ->buildMissingKeys($toCheckArrayKeys, $masterArrayKeys);
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
        $toFile  = $this->pathToSave.$messageFile;

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
        
        $this->registerUpdate($messageFile);

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

    private function buildMissingKeys($reference, $test)
    {
        $referenceKeys = array_keys($reference);
        $testKeys = array_keys($test);
        
        $missingKeys = array();
        $missingKeys = array_diff($referenceKeys, $testKeys);

        return $missingKeys;
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
        $fileContent = $this->readFileMessage($path);
     
        return $this->readKeys($fileContent);
    }
     
    private function readKeys($file)
    {
        $keys = [];
     
        foreach ($file as $key => $line) {
            if (strpos($line, '<message key="') === false) {
                continue;
            }
     
            $key = $this->getKey($line);
     
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

    /**
     * Gets the value of inMasterNotInToCheck.
     *
     * @return mixed
     */
    public function getInMasterNotInToCheck()
    {
        return $this->inMasterNotInToCheck;
    }

    /**
     * Gets the value of inToCheckNotInMaster.
     *
     * @return mixed
     */
    public function getInToCheckNotInMaster()
    {
        return $this->inToCheckNotInMaster;
    }
}
