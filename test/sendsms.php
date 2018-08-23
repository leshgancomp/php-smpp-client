<?php

require_once dirname(__FILE__) . '/../src/smpp-client/GsmEncoder.php';
require_once dirname(__FILE__) . '/../src/smpp-client/SmppClient.php';
require_once dirname(__FILE__) . '/../src/smpp-client/SocketTransport.php';
require_once dirname(__FILE__) . '/../src/smpp-client/SocketTransportException.php';

// Construct transport and client
$transport = new SocketTransport(array('127.0.0.1'), 2775);
$transport->setRecvTimeout(100000);
$smpp = new SmppClient($transport);
// Activate binary hex-output of server interaction
$smpp->debug = true;
$transport->debug = true;
// Open the connection
$transport->open();
$smpp->bindTransmitter("demouser", "demopass");
// Optional connection specific overrides
//SmppClient::$sms_null_terminate_octetstrings = false;
//SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
//SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;
// Prepare message
$tags = array();
$from = new SmppAddress('SMSIND', SMPP::TON_ALPHANUMERIC);
$to = new SmppAddress(919942012345, SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);
for ($i = 0; $i < 1; $i++) {
    $message = 'Hello sms from Mr.ABC' . $i . '.';
//$encodedMessage = $message;
    $encodedMessage = utf8_encode($message);
//  $encodedMessage = GsmEncoder::utf8_to_gsm0338($message);
    // Send
    $msgid = $smpp->sendSMS($from, $to, $encodedMessage, $tags);
    print 'message ref id: ' . $msgid;
}
// Close connection
$smpp->close();
