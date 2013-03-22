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

class Reader {

    /**
     * @var string $device
     */
    protected $device = '/dev/ttyUSB0';

    /**
     * Constructeur
     */
    public function __construct($device = null)
    {
        if (!is_null($device)) {
            $this->device = $device;
        }
    }

    /**
     * Récupère une trame sur le périphérique
     *
     * @return string
     */
    public function getFrame()
    {
        $trame = '';
        $handle = fopen($this->device, 'r');

        if ($handle) {
            // read device while not start of text
            while(fread($handle,1) != chr(2));

            // start of text
            do {
                $char = fread($handle, 1);
                if ($char != chr(2)) {
                    $trame .= $char;
                }
            } while ($char != chr(2));
            // close
            fclose($handle);

            // clean content
            $trame = substr($trame, 1, -1);
            return $trame;
        } else {
            throw new \LogicException('Impossible d\'accéder à la télé-info !');
        } 
    }
}
