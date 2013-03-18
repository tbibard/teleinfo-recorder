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

use TeleinfoRecorder\Formatter\FormatterInterface;

abstract class AbstractHandler implements HandlerInterface
{
    protected $formatter;
    
    abstract protected function write(array $record);

    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    protected function getFormatter()
    {
        if (!$this->formatter) {
            $this->formatter = $this->getDefaultFormatter();
        }

        return $this->formatter;
    }


    public function handle(array $record)
    {
        if (!is_null($this->getFormatter())) {
            $record['formatted'] = $this->getFormatter()->format($record);
        }

        $this->write($record);
    }
}
