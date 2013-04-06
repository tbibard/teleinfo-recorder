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

class SumFieldsProcessorTest extends TestCase
{
    public function testProcessor()
    {
        $record = $this->getRecord();

        $processor = new SumFieldsProcessor(array('IINST', 'IMAX'));
        $this->assertEquals(48, call_user_func($processor, $record));
    }
}
