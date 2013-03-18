<?php

/*
 * This file is part of the TeleinfoRecorder package.
 *
 * (c) Thomas Bibard <thomas.bibard@neblion.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TeleinfoRecorder\Formatter;

class CsvFormatter implements FormatterInterface
{
    protected $delimiter = ";";
    protected $enclosure = "\"";

    public function __construct(array $options = array())
    {
        if (is_array($options)) {
            // set delimiter
            if (array_key_exists('delimiter', $options)) {
                if (strlen($options['delimiter']) == 1) {
                    $this->delimiter = $options['delimiter'];
                }
            }
            // set enclosure
            if (array_key_exists('enclosure', $options)) {
                if (strlen($options['enclosure']) == 1) {
                    $this->enclosure = $options['enclosure'];
                }
            }
        }

    }

    public function format(array $record)
    {
        $str = '';
        foreach ($record as $value) {
            $str .= $this->enclosure . $value . $this->enclosure . $this->delimiter;
        }
        return $str;
    }
}
