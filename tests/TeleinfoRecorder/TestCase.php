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

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * return array record
     */
    public function getRecord()
    {
        return array(
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
        );
    }
}
