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

class CosmFeedHandler extends AbstractHandler
{

    protected $feedId = null;
    protected $apiKey = null;
    protected $datastreams = array();

    public function __construct($apiKey, $feedId, $datastreams)
    {
        $this->apiKey       = $apiKey;
        $this->feedId       = $feedId;
        $this->datastreams  = $datastreams;
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
        if (empty($this->datastreams)) {
            return null;
        }

        $datastreams = '';

        $client = new Client('http://api.cosm.com');
        foreach ($this->datastreams as $key) {
            if (array_key_exists($key, $record)) {
                if (!empty($datastreams)) {
                    $datastreams .= ',';
                }
                $datastreams .= '{ "id":"' . $key . '", "current_value":"' . $record[$key] . '"}';
            }
        }

        $request = $client->put('/v2/feeds/' . $this->feedId, null, '{
            "version": "1.0.0",
            "datastreams": [' . $datastreams . ']
        }');

        $request->addHeader('X-ApiKey', $this->apiKey);
        $response = $request->send();
    }
}
