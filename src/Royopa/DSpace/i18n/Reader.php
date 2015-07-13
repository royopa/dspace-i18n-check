<?php

/**
 * Reader XML
 * @author royopa - <royopa@gmail.com>
 */

namespace Royopa\DSpace\i18n;

class Reader
{
    public function __construct()
    {
        $pathToSave = __DIR__ . '/../../../../sources/';
        
        $master = 'messages.xml';
        $this->fullPathMaster = $pathToSave.$master;

        $toCheck = 'messages_pt_BR.xml';
        $this->fullPathToCheck = $pathToSave.$toCheck;

        $reader = new \Sabre\Xml\Reader();
        $reader->open($this->fullPathMaster);
        $reader->elementMap = [
            '{http://apache.org/cocoon/i18n/2.1}catalogue' => 'Sabre\Xml\Element\KeyValue',
        ];
        
        $rows = array();
        $output = array();

        foreach ($reader->parse() as $key => $row) {
            if (! is_array($row) || count($row) == 1) {
                continue;
            }

            $rows = $row;
        }

        foreach ($rows as $key => $row) {
            $key   = $row['attributes']['key'];
            $value = $row['value'];

            $output[$key] = $value;
        }

        var_dump($output);

        $reader->close();
    }
}
