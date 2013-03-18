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

use TeleinfoRecorder\Formatter\CsvFormatter;

class StreamHandler extends AbstractHandler
{

    protected $stream = null;

    public function __construct($stream)
    {
        if (is_resource($stream)) {
            if (get_resource_type($stream) == 'file') {
                $this->stream = $stream;
            } else {
                throw new \LogicException('Your stream resource is not a file resource !');
            }
        } else {
            $this->stream = fopen($stream, 'a');
        }
    }

    protected function getDefaultFormatter()
    {
        return new CsvFormatter();
    }

    public function write(array $record)
    {
        fwrite($this->stream, (string) $record['formatted'] . "\n");
    }
}
