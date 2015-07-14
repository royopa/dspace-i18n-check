<?php

/**
 * Reader XML
 * @author royopa - <royopa@gmail.com>
 */

namespace Royopa\DSpace\i18n;

class Reader
{
    public function getArrayKeysAndValues($pathFile)
    {
        $reader = new \Sabre\Xml\Reader();
        $reader->open($pathFile);
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

        $reader->close();

        foreach ($rows as $key => $row) {
            $key   = $row['attributes']['key'];
            $value = $row['value'];

            $output[$key] = $value;
        }

        return $output;
    }
}
