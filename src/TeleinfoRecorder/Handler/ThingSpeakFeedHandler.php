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

class ThingSpeakFeedHandler extends AbstractHandler
{

    protected $feedId = null;
    protected $apiKey = null;
    protected $fields = array();

    public function __construct($apiKey, $feedId, $fields)
    {
        $this->apiKey   = $apiKey;
        $this->feedId   = $feedId;
        $this->fields   = $fields;
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
        if (!is_array($this->fields) or empty($this->fields) or count($this->fields) > 8) {
            return null;
        }

        $postData = array();
        $postData['Channel'] = $this->feedId;
        foreach ($this->fields as $index => $field) {
            $postData['field' . ($index+1)] = $record[$field];
        }

        $client = new Client('https://api.thingspeak.com');
        $request = $client->post('/update/', null, $postData);
        $request->addHeader('X-THINGSPEAKAPIKEY', $this->apiKey);
        $response = $request->send();
    }
}
