<?php

/*
 * This file is part of the TeleinfoRecorder package.
 *
 * (c) Thomas Bibard <thomas.bibard@neblion.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeleinfoRecorder\Processor;

class VariationLastProcessor
{
    protected $key      = null;
    protected $path     = '/tmp/';
    protected $period   = null;

    public function __construct($key, $path, $period = null)
    {
        $this->key      = $key;
        $this->path     = $path;
        $this->period   = $period;
    }


    /**
     *
     * @param array $record
     * @return array
     */
    public function __invoke($record)
    {
        // store the read value
        $write = $record[$this->key];

        $filename = $this->path . '/' . basename(str_replace('\\', '/', get_class($this))) .
            '/' . $this->key;

        if (file_exists($filename)) {
            // read previous store value
            $readStr = file_get_contents($filename);
            $read = explode(';', $readStr);

            // calculate variation
            $value =  $record[$this->key] - $read[0];

            if (!empty($this->period)) {
                $periodDiff = strtotime('now') - $read[1];
                if ($periodDiff >= $this->period * 2) {
                    $value = floor($value / ($periodDiff / $this->period));
                }
            }
        } else {
            // check if dir exist
            if (!file_exists(dirname($filename))) {
                mkdir(dirname($filename));
            }
            // return 0 if the previous value is unknown
            $value = 0;
        }

        // write last read value in file
        file_put_contents($filename, $write . ';' . strtotime('now'));

        return $value;
    }

}
