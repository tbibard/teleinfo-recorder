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

use TeleinfoRecorder\TestCase;
use TeleinfoRecorder\Recorder;
use TeleinfoRecorder\Handler\StreamHandler;
use TeleinfoRecorder\Processor\CopyProcessor;
use TeleinfoRecorder\Processor\SumFieldsProcessor;

class RecorderTest extends TestCase
{
    /**
     * @covers TeleinfoRecorder\Recorder::getName
     * @covers TeleinfoRecorder\Recorder::setName
     */
    public function testGetName()
    {
        $recorder = new Recorder();
        $recorder->setName('foo');
        $this->assertEquals('foo', $recorder->getName());
    }

    /**
     * @covers TeleinfoRecorder\Recorder::pushProcessor
     */
    public function testPushProcessor()
    {
        $recorder = new Recorder();
        $copy = new CopyProcessor('HCHC');
        $this->assertEquals(1, $recorder->pushProcessor($copy, 'Key1'));
        $this->assertEquals(2, $recorder->pushProcessor($copy, 'Key1'));
        $this->assertNotEquals(2, $recorder->pushProcessor($copy, 'Key1'));
    }

    /**
     * @covers TeleinfoRecorder\Recorder::pushHandler
     */
    public function testPushHandler()
    {
        $recorder = new Recorder();
        $this->assertEquals(1, $recorder->pushHandler(new StreamHandler('/tmp/file1.txt')));
        $this->assertEquals(2, $recorder->pushHandler(new StreamHandler('/tmp/file2.txt')));
        $this->assertNotEquals(2, $recorder->pushHandler(new StreamHandler('/tmp/file3.txt')));
    }

    /**
     * @covers TeleinfoRecorder\Recorder::popHandler
     */
    public function testPopHandler()
    {
        $recorder = new Recorder();
        $handler1 = new StreamHandler('/tmp/file1.txt');
        $handler2 = new StreamHandler('/tmp/file2.txt');
        $recorder->pushHandler($handler1);
        $recorder->pushHandler($handler2);
        $this->assertEquals($handler2, $recorder->popHandler());
        $this->assertEquals($handler1, $recorder->popHandler());
        //$this->assertInstanceOf('LogicException', $recorder->popHandler());
    }

    /**
     * @covers TeleinfoRecorder\Recorder::isValidMessage
     * @covers TeleinfoRecorder\Recorder::_calculateCheckSum
     */
    public function testIsValidMessage()
    {
        $recorder = new Recorder();
        $this->assertTrue($recorder->isValidMessage('HCHC 028835516 ,'));
        $this->assertTrue($recorder->isValidMessage('HCHP 053250330 ('));
        $this->assertFalse($recorder->isValidMessage('HCHP 053250330 P'));
    }

    /**
     * @covers TeleinfoRecorder\Recorder::getRecord
     */
    public function testgetRecord()
    {
        $recorder = new Recorder($this->__getReader());
        $this->assertEquals(array(
                'ADCO'      => '020422624973',
                'OPTARIF'   => 'HC..',
                'ISOUSC'    => '45',
                'HCHC'      => '028835516',
                'HCHP'      => '053241739',
                'PTEC'      => 'HP..',
                'IINST'     => '007',
                'IMAX'      => '041',
                'PAPP'      => '01720',
                'HHPHC'     => 'D',
                'MOTDETAT'  => '000000'
            ), $recorder->getRecord());
    }

    /**
     * @covers TeleinfoRecorder\Recorder::isValidRecord
     * @covers TeleinfoRecorder\Recorder::__checkRecord
     */
    public function testIsValidRecord()
    {
        $recorder = new Recorder();
        $this->assertTrue($recorder->isValidRecord(array('ADCO' => '45454545')));
        $this->assertFalse($recorder->isValidRecord(array('AAAADCO' => '45454545')));
    }

    /**
     */
    public function testProcessorsWithExternalKeys()
    {
        /*
        $reader = $this->__getReader();

        $reflection_class = new \ReflectionClass('\\TeleinfoRecorder\\Recorder');
        $method = $reflection_class->getMethod('__processorsWithExternalKeys');
        $method->setAccessible(true);

        $recorder = new Recorder($reader);
        $sumconso = new SumFieldsProcessor(array('HCHP', 'HCHC'));
        $recorder->pushProcessor($sumconso, 'CONSO');
        $record = $recorder->getRecord();
        $this->assertEquals(array(
                'ADCO'      => '020422624973',
                'OPTARIF'   => 'HC..',
                'ISOUSC'    => '45',
                'HCHC'      => '028835516',
                'HCHP'      => '053241739',
                'PTEC'      => 'HP..',
                'IINST'     => '007',
                'IMAX'      => '041',
                'PAPP'      => '01720',
                'HHPHC'     => 'D',
                'MOTDETAT'  => '000000',
                'CONSO'     => 82077255
            ), $method->invoke($recorder, $record));
         */
    }

    /**
     *
     *
     */
    private function __getReader()
    {
        // Création bouchon pour _readFrame
        $reader = $this->getMock('TeleinfoRecorder\Reader');
        $reader->expects($this->any())
            ->method('getFrame')
            ->will($this->returnValue('ADCO 020422624973 @

OPTARIF HC.. <

ISOUSC 45 ?

HCHC 028835516 ,

HCHP 053241739 5

PTEC HP..  

IINST 007 ^

IMAX 041 D

PAPP 01720 +

HHPHC D /

MOTDETAT 000000 B'));

    return $reader;

    }
}
