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
     * @var string $device
     */
    protected $device = '/dev/ttyUSB0';
    /**
     * var array $handlers
     */
    protected $handlers = array();

    /**
     * Constructor
     *
     * @param string    $name Name of the counter
     * @param string    $device Name of the device
     */
    public function __construct($name, $device = null)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('You have to set name of the counter !');
        } else {
            $this->name = $name;
        }


        if (!is_null($device)) {
            $this->device = $device;
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

    /**
     * Read a record
     *
     */
    public function readRecord()
    {
        $read = array();
        $contents = '';
        $handle = fopen($this->device, 'r');

        if ($handle) {
            // read device while not start of text
            while(fread($handle,1) != chr(2));

            // start of text
            do {
                $char = fread($handle, 1);
                if ($char != chr(2)) {
                    $contents .= $char;
                }
            } while ($char != chr(2));
            // close
            fclose($handle);

            // clean content
            $contents = substr($contents, 1, -1);

            // create array frame
            $frame = explode(chr(10).chr(10), $contents);

            // read each message in frame
            foreach($frame as $message) {
                $message = explode(chr(32), $message, 3);
                list($key, $value, $checksum) = $message;
                if (!empty($key)) {
                    $read[$key] = $value;
                }
            }

            $date = new \DateTime('now');
            $read['datetime'] = $date->format('Y-m-d H:i:s');

            return $read;
        }
    }

    /**
     * Check if a read record is valid
     */
    private function __checkRecord(array $record)
    {
        $fields = array('datetime', 'ADCO', 'OPTARIF', 'ISOUSC', 'HCHP', 'HCHC', 
            'PTEC', 'IINST', 'IMAX', 'PAPP', 'HHPHC', 'MOTDETAT');

        // Count fields number
        if (count($record) != count($fields)) {
            throw new \InvalidArgumentException('Number of fields is not valid !');
        }

        $fieldsKeys = array_flip($fields);

        foreach ($record as $key => $value) {
            if (!array_key_exists($key, $fieldsKeys)) {
                throw new \LogicException($key . ' is not a valid key for record !');
            }
        }

        return true;
    }

    /**
     * Write record
     */
    public function writeRecord(array $record)
    {
        if (!$this->__checkRecord($record)) {
            throw new \LogicException('Record is not a valid record!');
        }

        foreach ($this->handlers as $handler) {
            $handler->handle($record);
        }
    }
}
