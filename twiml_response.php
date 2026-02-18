<?php
/**
 * TwiML Response for Twilio Call Flow
 * This file defines what happens during the call
 */

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<Response>
    <Say voice="alice" language="en-IN">
        Hello! Thank you for contacting FurryMart, your pet's paradise. 
        Please hold while we connect you to our customer care team.
    </Say>
    <Play>http://com.twilio.sounds.music.s3.amazonaws.com/MARKOVCHAINSHIMMEREACHGLOWWITHME.mp3</Play>
    <Dial timeout="30" callerId="+919925708543">
        <Number>+919925708543</Number>
    </Dial>
    <Say voice="alice" language="en-IN">
        We're sorry, but all our team members are currently busy. 
        Please try again later or send us a message on WhatsApp at +91 99257 08543.
    </Say>
</Response>
