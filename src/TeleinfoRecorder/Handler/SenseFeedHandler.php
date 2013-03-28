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

use Guzzle\Http\Client;

class SenseFeedHandler extends AbstractHandler
{

    protected $feeds = null;
    protected $apiKey = null;

    public function __construct($apiKey, $feeds)
    {
        $this->apiKey   = $apiKey;
        $this->feeds    = $feeds;
    }

    protected function getDefaultFormatter()
    {
        return null;
    }

    /**
     * Write
     *
     * TODO: check the response
     */
    public function write(array $record)
    {
        if (empty($this->feeds)) {
            return null;
        }

        $client = new Client('http://api.sen.se');
        foreach ($this->feeds as $key => $feedId) {
            if (array_key_exists($key, $record)) {
                $date = new \DateTime($record['datetime']);

                $request = $client->post('/events', null, '{
                            "feed_id": ' . $feedId . ',
                            "value": ' . $record[$key] . ',
                            "timetag": "' . $date->format('c') . '"
                }');

                $request->addHeader('sense_key', $this->apiKey);
                $response = $request->send();
            }
        }
    }
}
