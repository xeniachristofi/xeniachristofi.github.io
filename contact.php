<?php
// require ReCaptcha class
require('recaptcha-master/src/autoload.php');



// an email address that will receive the email with the output of the form
$sendTo = 'Contact form <xeniachristofi@gmail.com>';

// subject of the email
$subject = 'Contact Form Email';

$fields = array('fullname' => 'Name','email' => 'Email', 'message' => 'Message');

$okMessage = 'Form submitted successfully';

// If something goes wrong, we will display this message.
$errorMessage = 'There was an error while submitting the form. Please try again later';

// ReCaptch Secret
$recaptchaSecret = '6Lc6UK4UAAAAAHevtHLxZ41U0Lp9fYoi8rBcJvXq';

// let's do the sending

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try {
    if (!empty($_POST)) {

        // validate the ReCaptcha, if something is wrong, we throw an Exception,
        // i.e. code stops executing and goes to catch() block
        
        if (!isset($_POST['g-recaptcha-response'])) {
            throw new \Exception('ReCaptcha is not set.');
        }

        // do not forget to enter your secret key from https://www.google.com/recaptcha/admin
        
        $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret, new \ReCaptcha\RequestMethod\CurlPost());
        
        // we validate the ReCaptcha field together with the user's IP address
        
        $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$response->isSuccess()) {
            throw new \Exception('ReCaptcha was not validated.');
        }
        

        if(isset($_POST['fullname'])) {
            $name = filter_var($_POST['fullname'], FILTER_SANITIZE_STRING);
    }
     
    if(isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        
    }
         if(isset($_POST['message'])) {
        $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    }
        
        $emailText = "You have a new message from your contact form\n=============================\n" . $email . "\n" ;
        $emailText.="\n";
        $emailText .=$name;
        $emailText.="\n";
        $emailText .= $message;
// an email address that will be in the From field of the email.
        $from = "$name <$email>";
        // All the neccessary headers for the email.
        $headers = array('Content-Type: text/plain; charset="UTF-8";',
            'From: ' . $from,
            'Reply-To: ' . $from,
            'Return-Path: ' . $from,
        );
        
        // Send email
        mail($sendTo, $subject, $emailText, implode("\r\n", $headers));

        $responseArray = array('type' => 'success', 'message' => $okMessage);
    }
} catch (\Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
} else {
    echo $responseArray['message'];
}
