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

class EmoncmsInputHandler extends AbstractHandler
{
    protected $node = null;
    protected $inputs = null;
    protected $apiKey = null;

    public function __construct($apiKey, $node, $inputs)
    {
        $this->apiKey   = $apiKey;
	$this->inputs   = $inputs;
	$this->node	= $node;
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
        if (empty($this->inputs)) {
            return null;
        }

	$client = new Client('http://emoncms.org');

	// Build query_path
	$query = '/input/post.json?node=' . $this->node;

	$data = array();
        foreach ($this->inputs as $input) {
            if (array_key_exists($input, $record)) {
		$data[$input] = $record[$input];
	    }
        }
        
	$query .=  '&json=' . urlencode(json_encode($data)) . '&apikey=' . $this->apikey;
	$request = $client->post($query);
        $response = $request->send();
    }
}
