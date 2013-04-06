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

class SumFieldsProcessor
{
    protected $fields   = array();

    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     *
     * @param mixed $record
     * @return mixed
     */
    public function __invoke($record)
    {
        $sum = 0;

        foreach ($this->fields as $field) {
            if (array_key_exists($field, $record)) {
                $sum +=  $record[$field];
            }
        }

        return $sum;
    }

}
