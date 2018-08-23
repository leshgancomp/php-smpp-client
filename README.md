PHP-based SMPP client lib
=============

This is a simplified SMPP client lib for sending or receiving smses through [SMPP v3.4](http://www.smsforum.net/SMPP_v3_4_Issue1_2.zip).

This lib  built based on [php-smpp](https://github.com/onlinecity/php-smpp)

Basic usage example
-----

To send a SMS you can do:

``` php
<?php
require_once 'smppclient.class.php';
require_once 'gsmencoder.class.php';
require_once 'sockettransport.class.php';

// Construct transport and client
$transport = new SocketTransport(array('smpp.provider.com'),2775);
$transport->setRecvTimeout(10000);
$smpp = new SmppClient($transport);

// Activate binary hex-output of server interaction
$smpp->debug = true;
$transport->debug = true;

// Open the connection
$transport->open();
$smpp->bindTransmitter("USERNAME","PASSWORD");

// Optional connection specific overrides
//SmppClient::$sms_null_terminate_octetstrings = false;
//SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
//SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;

// Prepare message
$message = 'Hello world';
$encodedMessage = GsmEncoder::utf8_to_gsm0338($message);
$from = new SmppAddress('SMPP Test',SMPP::TON_ALPHANUMERIC);
$to = new SmppAddress(919942012345,SMPP::TON_INTERNATIONAL,SMPP::NPI_E164);

// Send
$smpp->sendSMS($from,$to,$encodedMessage,$tags);

// Close connection
$smpp->close();
```

F.A.Q.
-----

**I can't send more than 160 chars**  
There are three built-in methods to send Concatenated SMS (csms); CSMS_16BIT_TAGS, CSMS_PAYLOAD, CSMS_8BIT_UDH. CSMS_16BIT_TAGS is the default, if it don't work try another.

**Is this lib compatible with PHP 5.2.x ?**  
It's tested on PHP 5.3, but is known to work with 5.2 as well.

**Why am I not seeing any debug output?**  
Remember to implement a debug callback for SocketTransport and SmppClient to use. Otherwise they default to [error_log](http://www.php.net/manual/en/function.error-log.php) which may or may not print to screen. 

**Why do I get 'res_nsend() failed' or 'Could not connect to any of the specified hosts' errors?**  
Your provider's DNS server probably has an issue with IPv6 addresses (AAAA records). Try to set ```SocketTransport::$forceIpv4=true;```. You can also try specifying an IP-address (or a list of IPs) instead. Setting ```SocketTransport:$defaultDebug=true;``` before constructing the transport is also useful in resolving connection issues.

**I tried forcing IPv4 and/or specifying an IP-address, but I'm still getting 'Could not connect to any of the specified hosts'?**  
It would be a firewall issue that's preventing your connection, or something else entirely. Make sure debug output is enabled and displayed. If you see something like 'Socket connect to 1.2.3.4:2775 failed; Operation timed out' this means a connection could not be etablished. If this isn't a firewall issue, you might try increasing the connect timeout. The sendTimeout also specifies the connect timeout, call ```$transport->setSendTimeout(10000);``` to set a 10-second timeout.

**Why do I get 'Failed to read reply to command: 0x4', 'Message Length is invalid' or 'Error in optional part' errors?**  
Most likely your SMPP provider doesn't support NULL-terminating the message field. The specs aren't clear on this issue, so there is a toggle. Set ```SmppClient::$sms_null_terminate_octetstrings = false;``` and try again.  

**What does 'Bind Failed' mean?**  
It typically means your SMPP provider rejected your login credentials, ie. your username or password.

**Can I test the client library without a SMPP server?**  
Many service providers can give you a demo account, but you can also use the [logica opensmpp simulator](http://opensmpp.logica.com/CommonPart/Introduction/Introduction.htm#simulator) (java) or [smsforum client test tool](http://www.smsforum.net/sctt_v1.0.Linux.tar.gz) (linux binary). In addition to a number of real-life SMPP servers this library is tested against these simulators.

**I have an issue that not mentioned here, what do I do?**  
Please obtain full debug information, and open an issue here on github. Make sure not to include the Send PDU hex-codes of the BindTransmitter call, since it will contain your username and password. Other hex-output is fine, and greatly appeciated. Any PHP Warnings or Notices could also be important. Please include information about what SMPP server you are connecting to, and any specifics.