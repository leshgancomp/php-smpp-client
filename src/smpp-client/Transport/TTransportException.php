<?php

namespace Exploit\Smpp\Transport;

use Exploit\Smpp\Transport as SmppTransport;

/**
 * Transport exceptions
 */
class TTransportException extends SmppTransport\TException {

    const UNKNOWN = 0;
    const NOT_OPEN = 1;
    const ALREADY_OPEN = 2;
    const TIMED_OUT = 3;
    const END_OF_FILE = 4;

    function __construct($message = null, $code = 0) {
        parent::__construct($message, $code);
    }

}
