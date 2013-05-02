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
            $this->initialize($record);
        }

        $data = array();
        foreach ($record as $key => $value) {
            $data[':' . $key] = $value;
        }

        try {
            $this->statement->execute($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function initialize($record)
    {
        $fields = array(
            'ADCO'      => 'varchar(12) NOT NULL',
            'OPTARIF'   => 'varchar(4)',
            'ISOUSC'    => 'tinyint(6)',
            'BASE'      => 'int(11)',
            'HCHP'      => 'int(11)',
            'HCHC'      => 'int(11)',
            'EJPHN'     => 'int(11)',
            'EJPHPM'    => 'int(11)',
            'PEJP'      => 'tinyint(6)',
            'BBRHCJB'   => 'int(11)',
            'BBRHPJB'   => 'int(11)',
            'BBRHCJW'   => 'int(11)',
            'BBRHPJW'   => 'int(11)',
            'BBRHCJR'   => 'int(11)',
            'BBRHPJR'   => 'int(11)',
            'PTEC'      => 'varchar(4)',
            'DEMAIN'    => 'varchar(4)',
            'IINST'     => 'tinyint(6)',
            'ADPS'      => 'tinyint(6)',
            'IMAX'      => 'tinyint(6)',
            'PAPP'      => 'tinyint(6)',
            'HHPHC'     => 'varchar(1)',
            'MOTDETAT'  => 'varchar(6)'
        );

        try {

            $sqlCreate = 'CREATE TABLE IF NOT EXISTS `' . $this->tablename . '` (' .
              '`datetime` datetime NOT NULL';
            // Parcours du reecord pour créer les champs de la trame
            // le record a été contrôlé, tous les champs sont donc connus
            foreach ($record as $key => $value) {
                if (array_key_exists($key, $fields)) {
                    $sqlCreate .= ', `' . $key . '` ' . $fields[$key];
                } else {
                    if ($key != 'datetime') {
                        $unknownKeysType = array('integer' => 'int(11)', 'string' => 'varchar(100)');
                        $sqlCreate .= ', `' . $key . '` ' . $unknownKeysType[gettype($record[$key])];
                    }
                }
            }
            $sqlCreate .= ')';

            $this->pdo->exec($sqlCreate );
            $this->initialized = true;

            $sqlInsert = 'INSERT INTO `' . $this->tablename . '` (';
            $fields = $values = '';
            foreach ($record as $key => $value) {
                if (!empty($fields)) {
                    $fields .= ',';
                    $values .= ',';
                }
                $fields .= '`' . $key . '`';
                $values .= ':' . $key;
            }
            $sqlInsert .= $fields . ') VALUES (' . $values . ')';
            $this->statement = $this->pdo->prepare($sqlInsert);
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}
