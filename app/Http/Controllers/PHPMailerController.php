<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\User;



class PHPMailerController extends Controller
{


    // ========== [ Compose Email ] ================
    public function composeEmail(Request $request)
    {



        //$user = User::where('email', $request->emailRecipient)->first();
        $user = User::where('email', $request->email)->first();

        if($user){
            $email = $user->email;
        }else{
            return response()->json([
                "status" => "failed",
                "message" => "email not found"
            ], 404);            
        }
        

        
        
       
        //require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);     // Passing `true` enables exceptions

        //$mail = new PHPMailer;
        // try {

        // Email server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST');             //  smtp host
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_EMAIL');   //  sender username
        $mail->Password = env('SMTP_PASSWORD');      // sender password
        $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
        //$mail->Port = env('SMTP_PORT');
        $mail->Port = '587';
        //$mail->SMTPDebug = 2; // Or use SMTP::DEBUG_SERVER for detailed output


        // Sender info
        $mail->setFrom(env('SMTP_EMAIL'), 'Nurakhiri Email Service');
        //$mail->addReplyTo('reply@example.com', 'SenderName');

        // Add a recipient
        //$mail->addAddress($request->emailRecipient);
        $mail->addAddress('noerakhiri@gmail.com');

        // $mail->addBCC('noerakhiri@gmail.com');
        //$mail->addBCC('bcc@example.com');

        // Set email format to HTML
        $mail->isHTML(true);

        // Mail subject
        $mail->Subject = 'Confirmation Email Registration User';
        $portal = 'http://localhost';
        $manager = 'http://localhost';
        $port = $request->target == "portal" ? $portal : $manager;
        // Mail body content
        $bodyContent = '<h3>Your verification email is:</h3>';
        $bodyContent .= '
<h3>Please Visit in : http://somplak.my.id</h3>
<br>
<h3>if you dont feel registered, ignore this email</h3>
<h>';
        $mail->Body    = $bodyContent;

        // Send email
        if (!$mail->send()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo,
            ], 500);
            //echo 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'Message has been sent to email : ' . $request->emailRecipient,
            ], 200);
            //echo 'Message has been sent.';
        }
    }
        
    
}
