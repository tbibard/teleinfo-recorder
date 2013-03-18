<?php

/*
 * This file is part of the TeleinfoRecorder package.
 *
 * (c) Thomas Bibard <thomas.bibard@neblion.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeleinfoRecorder\Handler;

class PdoHandler extends AbstractHandler
{

    protected $pdo = null;
    protected $tablename = 'Releve';
    protected $initialized = false;
    protected $statement = null;

    public function __construct(\PDO $pdo, $tablename = '')
    {
        $this->pdo = $pdo;

        if (!empty($tablename)) {
            $this->tablename = $tablename;
        }
    }

    protected function getDefaultFormatter()
    {
        return null;
    }

    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        try {
        $this->statement->execute(array(
            ':datetime' => $record['datetime'],
            ':ptec'     => $record['ptec'],
            ':hp'       => $record['hp'],
            ':hc'       => $record['hc'],
            ':iinst'    => $record['iinst'],
            ':papp'     => $record['papp'],
        ));
        } catch (\PDOException $e) {
            echo $e->getMessage() . "\n";
        }
        echo "execute\n";
    }

    private function initialize()
    {
        echo 'initialize....' . "\n";

        try {



        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS `' . $this->tablename . '` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `datetime` datetime NOT NULL,
              `ptec` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
              `hp` int(11) NOT NULL,
              `hc` int(11) NOT NULL,
              `iinst` int(11) NOT NULL,
              `papp` int(11) NOT NULL,
              PRIMARY KEY (`id`)
            )'
        );

        $this->statement = $this->pdo->prepare(
            'INSERT INTO `' . $this->tablename .
                '`(`datetime`, `ptec`, `hp`, `hc`, `iinst`, `papp`)' .
                ' VALUES (:datetime, :ptec, :hp, :hc, :iinst, :papp)'
        );
            echo "jhjkhjkhjkhjkhjk\n";

        } catch (\PDOException $e) {
            throw $e;
        }
        $this->initialized = true;
    }
}
