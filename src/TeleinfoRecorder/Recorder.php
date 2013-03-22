<?php

/*
 * This file is part of the TeleinfoRecorder package.
 *
 * (c) Thomas Bibard <thomas.bibard@neblion.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeleinfoRecorder;

use TeleinfoRecorder\Handler\HandlerInterface;

class Recorder {

    /**
     * @var string $name
     */
    protected $name = '';

    /**
     * @var TeleinfoRecorder/Reader $reader
     */
    protected $reader = null;

    /**
     * var array $handlers
     */
    protected $handlers = array();

    /**
     * Constructor
     *
     * @param TeleinfoRecorder/Reader    $reader Teleinfo reader
     */
    public function __construct($reader = null)
    {
        if (!is_null($reader)) {
            $this->reader = $reader;
        } else {
            $this->reader = new Reader();
        }
    }

    /**
     * Set name of the counter
     *
     * @param string    $name Name of the counter
     */
    public function setName($name) 
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('You have to set name of the counter !');
        } else {
            $this->name = $name;
        }
    }

    /**
     * Return name of the counter
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Pushes a handler on to the stack.
     *
     * @param HandlerInterface $handler
     */
    public function pushHandler(HandlerInterface $handler)
    {
        return array_unshift($this->handlers, $handler);
    }

    /**
     * Pops a handler from the stack
     *
     * @return HandlerInterface
     */
     public function popHandler()
     {
         if (!$this->handlers) {
             throw new \LogicException('You tried to pop from an empty handler stack.');
         }

         return array_shift($this->handlers);
     }

    private function __calculateCheckSum($message)
    {
        // on retire le checksum fournit
        $msg  = trim(substr($message, 0, -1));
        // checksum
        $sum = 0;
        for ($i = 0; $i < strlen($msg); $i++) {
            $sum += ord($msg[$i]);
        }
        $sum = $sum & 0x3F;
        $sum += 0x20;
        return chr($sum);
    }

    public function isValidMessage($message) {
        $read = substr($message, -1);
        $calc = $this->__calculateCheckSum($message);
        if ($read === $calc) {
            return true;
        }

        return false;
    }

    /**
     * Read a record
     *
     */
    public function getRecord()
    {
        $read = array();

        $trame = $this->reader->getFrame();

        // create array frame
        $lignes = explode(chr(10).chr(10), $trame);

        // read each message in frame
        foreach($lignes as $message) {
            if (!$this->isValidMessage($message)) {
                throw new \LogicException('Checksum n\'est pas valide, la trame a pu être altérée !');
            }
            $elements = explode(chr(32), $message, 3);
            list($key, $value, $checksum) = $elements;
            if (!empty($key)) {
                $read[$key] = $value;
            }
        }

        return $read;
    }

    /**
     * Contrôle si l'enregistrement est valide, si il respecte les spécifications ERDF
     *
     * Ce contrôle ce borne pour le moment à vérifier si les clefs sont des clefs connus
     * dans la spécification. Contrôle fonctionnel pour les compteurs de type:
     * monophasé multarifs CBEMM + évolution ICC
     *
     * ToDO: gestion compteurs triphasé (CBETM)
     * ToDO: gestion compteurs Jaune (CJE)
     */
    private function __checkRecord(array $record)
    {
        $fields = array(
            'ADCO', 'OPTARIF', 'ISOUSC', 'BASE',
            'HCHP', 'HCHC', // Option heures creuses
            'EJPHN', 'EJPHPM', 'PEJP', // Option EJP
            'BBRHCJB', 'BBRHPJB', 'BBRHCJW', 'BBRHPJW', 'BBRHCJR', 'BBRHPJR', // Option Tempo
            'PTEC', 'DEMAIN', 'IINST', 'ADPS', 'IMAX', 'PAPP', 'HHPHC', 'MOTDETAT'
        );

        $fieldsKeys = array_flip($fields);

        foreach ($record as $key => $value) {
            if (!array_key_exists($key, $fieldsKeys)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Contrôle si un enregistrement est valide
     *
     * @parma array $record
     * @return bool
     */
    public function isValidRecord(array $record)
    {
        if ($this->__checkRecord($record)) {
            return true;
        }

        return false;
    }

    /**
     * Write record
     */
    public function write()
    {
        $record = $this->getRecord();

        if (!$this->__checkRecord($record)) {
            throw new \LogicException('Record is not a valid record!');
        }

        // ajout de la date et de l'heure de la lecture
        $date = new \DateTime('now');
        $record['datetime'] = $date->format('Y-m-d H:i:s');

        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }
    }
}
