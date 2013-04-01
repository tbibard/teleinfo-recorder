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
    protected $path = '/tmp/';

    public function __construct($path)
    {
        $this->path = $path;
        echo 'path: ' . $path . "\n";
    }


    /**
     *
     * @param mixed $value
     * @return mixed
     */
    public function __invoke($key, $value)
    {
        // store the read value
        $write = $value;

        $filename = $this->path . '/' . basename(str_replace('\\', '/', get_class($this))) .
            '/' . $key;

        if (file_exists($filename)) {
            // read previous store value
            $read = file_get_contents($filename);
            // calculate variation
            $value =  $value - $read;
        } else {
            // check if dir exist
            if (!file_exists(dirname($filename))) {
                mkdir(dirname($filename));
            }
            // return 0 if the previous value is unknown
            $value = 0;
        }

        // write last read value in file
        file_put_contents($filename, $write);

        return $value;
    }

}
