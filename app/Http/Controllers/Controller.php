<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{

    function JSONError(){
        return response()->json(["status" => app('Illuminate\Http\Response')->status()])
        ->header("Content-type", "application/json; charset=utf-8")
        ->header("Access-Control-Allow-Origin", "*")
        ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
        ->setEncodingOptions(JSON_PRETTY_PRINT);
    }

    function SuccessFull($status,$count,$data){
        return response()->json(["status" => $status,"count" => $count, "data" => $data])
                            ->header("Content-type", "application/json; charset=utf-8")
                            ->header("Access-Control-Allow-Origin", "*")
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
                            ->setEncodingOptions(JSON_PRETTY_PRINT);
    }
    
    function SuccessAPI($status,$data){
        return response()->json(["status" => $status, "data" => $data])
                            ->header("Content-type", "application/json; charset=utf-8")
                            ->header("Access-Control-Allow-Origin", "*")
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
                            ->setEncodingOptions(JSON_PRETTY_PRINT);
    }

    function SuccessAPIv2($head,$body,$message){

        if(app('Illuminate\Http\Response')->status()==500){
            JSONError();
        }else{
            if(empty($message)){$message=null;}
            return response()->json(["HEAD" => $head, "BODY" => $body, "MESSAGE" => $message])
                                ->header("Content-type", "application/json; charset=utf-8")
                                ->header("Access-Control-Allow-Origin", "*")
                                ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
                                ->setEncodingOptions(JSON_PRETTY_PRINT);
        }

    }


    // function SuccessAPIv1($status,$data){

    //     if(app('Illuminate\Http\Response')->status()==500){
    //         JSONError();
    //     }else{
    //         return response()->json(["status" => $status, "data" => $data])
    //                             ->header("Content-type", "application/json; charset=utf-8")
    //                             ->header("Access-Control-Allow-Origin", "*")
    //                             ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
    //                             ->setEncodingOptions(JSON_PRETTY_PRINT);
    //     }

    // }
    
    // function SuccessAPIv2($status,$count,$data){

    //     if(app('Illuminate\Http\Response')->status()==500){
    //         JSONError();
    //     }else{
    //         return response()->json(["status" => $status, "count" => $count, "data" => $data])
    //                             ->header("Content-type", "application/json; charset=utf-8")
    //                             ->header("Access-Control-Allow-Origin", "*")
    //                             ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
    //                             ->setEncodingOptions(JSON_PRETTY_PRINT);
    //     }

    // }

    // function SuccessAPIv3($status,$message,$count,$data){

    //     if(app('Illuminate\Http\Response')->status()==500){
    //         JSONError();
    //     }else{
    //         return response()->json(["status" => $status, "message" => $message, "count" => $count, "data" => $data])
    //                             ->header("Content-type", "application/json; charset=utf-8")
    //                             ->header("Access-Control-Allow-Origin", "*")
    //                             ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
    //                             ->setEncodingOptions(JSON_PRETTY_PRINT);
    //     }

    // }
    
    function responseSuccess($a)
    {
        return response()->json(["status" => "1", "data" => $a])
            ->header("Access-Control-Allow-Origin", "*")
            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS");
    }

}
