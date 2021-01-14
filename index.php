<?php
/** Plugin name: Custom email sender */

function send_email() {
    require("sendgrid-php.php");
    $entityBody = file_get_contents('php://input');
    $data = json_decode($entityBody);
    $email = new \SendGrid\Mail\Mail(); 
    $email->setFrom("from@gmail.com");
    $email->setSubject("Poruka od {$data->name}");
    $email->addTo("to@gmail.com");
    $email->addContent(
        "text/html", " 
        <div style='background-color: rgba(241, 243, 244, .6); border-radius: 10px; padding: 10px 20px;'>
            <h2 style='font-size: 25px; margin: 0;'>Poruka od <span style='font-size: 30px;'>{$data->name}</span></h2>
            <h3 style='font-size: 22px'>Email: <span style='color: #E50046 !important;'>{$data->email}</span></h3>
            <p style='font-size: 18px'>Poruka: <br> {$data->poruka}</p>
        </div>"
    );
    $sendgrid = new \SendGrid('API_KEY');
    try {
        $response = $sendgrid->send($email);
        print $response->statusCode() . "\n";
        print_r($response->headers());
        print $response->body() . "\n";
        return $entityBody;
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}


add_action('rest_api_init', function() {
    register_rest_route('email', 'send', [
        'methods' => 'POST',
        'callback' => 'send_email',
    ]);
});