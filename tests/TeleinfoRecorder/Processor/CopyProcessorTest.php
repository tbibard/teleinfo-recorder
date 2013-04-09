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

use TeleinfoRecorder\TestCase;

class CopyProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $record = $this->getRecord();
        $processor = new CopyProcessor('HCHC');
        $this->assertEquals('028835516', call_user_func($processor, $record));
    }
}
