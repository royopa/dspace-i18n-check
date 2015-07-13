<?php

/**
 * Get last updates in database
 * @author royopa - <royopa@gmail.com>
 */

namespace Royopa\DSpace\i18n;

use Doctrine\DBAL\Connection;

class SourcesUpdate
{
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function getLastUpdate($name_source = 'messages.xml')
    {
        $sql = 'SELECT * FROM update_source where name_source = ? ORDER BY date DESC LIMIT 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $name_source);
        $stmt->execute();
        $row = $stmt->fetchAll();

        if (! $row) {
            return false;
        }

        return $row[0];
    }
}
