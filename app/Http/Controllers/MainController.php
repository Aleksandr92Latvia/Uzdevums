<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reports;

use Validator;
use Session;

class MainController extends Controller
{
    public function start(){
        $rewiews = new Reports();
        return view('main', ['rewiews'=> $rewiews->all()]);
    }
   
    public function check(Request $request){
        
        $input = $request->all();

        $valid = Validator::make($input,[
            'myName' => 'required',
            'myEmail' => 'required',
            'myMessage' => 'required',
            'browser' => 'required',
            'IP_adress' => 'required',
            'g-recaptcha-response' => function ($atribute, $value, $fail){
                $secretKey = config('services.recaptcha.secret');
                $response = $value;
                $userIP = $_SERVER['REMOTE_ADDR'];
                $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$response&remoteip=$userIP";
                $response = \file_get_contents($url);
                $response = json_decode($response);
                if(!$response->success){
                    $fail($atribute.'fail');
                }
            }
        ]);
        if ($valid->passes()){
            $report = new Reports();
            $report->myName = $request->input('myName');
            $report->myEmail = $request->input('myEmail');
            $report->myUrl = $request->input('myUrl');
            $report->myMessage = $request->input('myMessage');
            $report->browser = $request->input('browser');
            $report->IP_adress = $request->input('IP_adress');

            $report->save();
        }
        return redirect()->route('main');
    }
}
