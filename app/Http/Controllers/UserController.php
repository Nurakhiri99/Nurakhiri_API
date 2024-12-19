<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    
    public function index(request $request)
    {

        if($request->sortBy == "" || $request->sortBy == null){
            $sortBy = "created_at";
        }else{
            $sortBy = $request->sortBy;
        }

        if($request->email == null || $request->email == "" && $request->name == null || $request->name == ""){ 
            $user = User::orderBy($sortBy, 'ASC')->where('active', '1')->get();
        }else{
            $user = DB::table('users')
            ->selectRaw('id,email,name,created_at')
            ->where('active', '1')
            ->where('email', $request->email)
            ->orWhere('name', $request->name)
            ->orderBy($sortBy, 'ASC')
            ->simplePaginate(5);
        }
        

        if (count($user) == 0) { 
            $user = User::orderBy($sortBy, 'ASC')->where('active', '1')->simplePaginate(5);
            return response()->json([
                'status' => 'success',
                'users' => $user,
            ]);
    
        }else{
            return response()->json([
                'status' => 'success',
                'users' => $user,
            ]);    
        }
    }


    //Created Add
    public function store(Request $request)
    {

        //define validation rules
        $validator = Validator::make($request->all(), [            
            'email' => 'string|required|email|unique:users,email,regex:/(.+)@(.+)\.(.+)/i',
            'password'=> 'string|required|min:8',
            'name' => 'string|required|min:3|max:50',
        ]);

         //check if validation fails
         if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'email'     => $request->email,
            'password'   => Hash::make($request->password),
            'name'     => $request->name,
            'active'     => 1,
        ]);


        $mail = new PHPMailer(true); 
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = env('SMTP_HOST');             //  smtp host
        $mail->SMTPAuth = true;
        $mail->Username = env('SMTP_EMAIL');   //  sender username
        $mail->Password = env('SMTP_PASSWORD');      // sender password
        $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
        $mail->Port = '587';

        $mail->setFrom(env('SMTP_EMAIL'), 'Nurakhiri Email Service');
        $mail->addAddress($request->email);
        $mail->addBCC('noerakhiri@gmail.com');
        $mail->isHTML(true);

        $mail->Subject = 'Confirmation Email Registration User';

        $bodyContent = '<h3>Your verification email is:</h3>';
        $bodyContent .= '
                        <h3>Please Visit in : http://testing.my.id</h3>
                        <h3> Email : ' . $request->email . '</h3>
                        <h3> name : ' . $request->name . '</h3>
                        <br>
                        <h3>if you dont feel registered, ignore this email</h3>
                        <h>';
        $mail->Body    = $bodyContent;

        if (!$mail->send()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo,
            ], 500);
            //echo 'Message could not be sent. Mailer Error: '.$mail->ErrorInfo;
        } else {
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully And Message has been sent to email : ' . $request->email,
                'user' => $user,
            ]);
        }   
   
    }

    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            'status' => 'success',
            'user' => $user,
        ]);
    }

}
