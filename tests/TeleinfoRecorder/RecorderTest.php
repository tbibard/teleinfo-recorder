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

use TeleinfoRecorder\Recorder;
use TeleinfoRecorder\Handler\StreamHandler;

class TeleinfoRecorderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers TeleinfoRecorder\Recorder::getName
     */
    public function testGetName()
    {
        $recorder = new Recorder('foo');
        $this->assertEquals('foo', $recorder->getName());
    }

    /**
     * @covers TeleinfoRecorder\Recorder::pushHandler
     */
    public function testPushHandler()
    {
        $recorder = new Recorder('foo');
        $this->assertEquals(1, $recorder->pushHandler(new StreamHandler('/tmp/file1.txt')));
        $this->assertEquals(2, $recorder->pushHandler(new StreamHandler('/tmp/file2.txt')));
    }

    /**
     * @covers TeleinfoRecorder\Recorder::popHandler
     */
    public function testPopHandler()
    {
        $recorder = new Recorder('foo');
        $handler1 = new StreamHandler('/tmp/file1.txt');
        $handler2 = new StreamHandler('/tmp/file2.txt');
        $recorder->pushHandler($handler1);
        $recorder->pushHandler($handler2);
        $this->assertEquals($handler2, $recorder->popHandler());
        $this->assertEquals($handler1, $recorder->popHandler());
    }
}
