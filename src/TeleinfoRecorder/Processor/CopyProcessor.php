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

class CopyProcessor
{
    protected $key = '';

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     *
     * @param mixed $record
     * @return mixed
     */
    public function __invoke($record)
    {
        return $record[$this->key];
    }

}
