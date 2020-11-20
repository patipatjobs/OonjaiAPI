<?php

namespace App\Http\Controllers;

use DB;

use App\Users;
use App\Profile;
use App\Token;
use App\Cars;
use App\Student;
use App\Busstatus;
use App\Busstatusdetails;

use App\Barcode;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CreateController extends Controller
{

    public function create(Request $request)
    {
        /*
            Register
            Add
        */
        $action = $request->action;
        $table = $request->table;

        //Register
        if($action == 'register'){

            //ตรวจสอบโซเซียล
            if($request->token_type == 'line' || $request->token_type == 'facebook' || $request->token_type == 'google'){

                //ตรวจสอบ userId
                $checkToken = DB::table('token')->where('userId',$request->userId)->count();

                //ยังไม่ลงทะเบียน
                if($checkToken==0){

                    //INSERT USERS
                    $data = Users::create(['isActive' => 1]);

                    //SHOW USERS ID
                    $tmp_users_ID = DB::table('users')->select('users_ID')->orderBy('users_ID','desc')->first();

                    //INSERT Profile
                    $data = new Profile();
                    $data->profile_prefix = $request->profile_prefix;
                    $data->profile_firstname = $request->profile_firstname;
                    $data->profile_lastname = $request->profile_lastname;
                    $data->profile_mobile = $request->profile_mobile;
                    $data->roles_ID = $request->roles_ID;
                    $data->users_ID = $tmp_users_ID->users_ID;
                    $data->images_url = $request->images_url;
                    $data->save();

                    //INSERT TOKEN
                    $data = DB::table('token')->insert([
                                'token_type' => $request->token_type,
                                'userId' => $request->userId,
                                'token_key' => $request->token_key,
                                'created_by' => $tmp_users_ID->users_ID
                            ]);
                    
                    //VIEW Profile
                    $body = DB::table('profile')->select('profile_firstname','profile_lastname','profile_mobile','roles_ID','users_ID','images_url','userId')
                                                ->where('users_ID',$tmp_users_ID->users_ID)
                                                ->join('token', 'token.created_by', '=', 'profile.users_ID')
                                                ->get();
                    $error = null;
                
                //ถ้าลงทะเบียนแล้ว
                }else{

                    $body =[];
                    $error = "Token Error";
                }
            
            //ถ้าไม่ใช่โซเชียล
            }else{

                $body =[];
                $error = "Type Error";
            }

            $head = '1';
            return $this->SuccessAPIv2($head,$body,$error);

        //Add
        }else if($action == 'add'){

            //Cars
            if($table == 'cars'){

                //ตรวจสอบว่าทะเบียนรถซ้ำหรือไม่
                $checkLisence = DB::table('cars')->where('cars_license','=',$request->cars_license)->where('isActive','=','1')->count();

                //ถ้าซ้ำ
                if($checkLisence!='0'){
                    
                    $body =[];
                    return $this->SuccessAPIv2('0',$body,"เลขทะเบียนมันซ้ำ");

                //ถ้าไม่
                }else{

                    //ค้นหาว่าเคยลงทะเบียนไว้หรือไม่
                    $checkDefault = DB::table('cars')->where('default','=','1')->where('created_by',$request->created_by)
                                                        ->where('isActive','=','1')->count();

                    //ไม่เคย
                    if($checkDefault=='0'){ 
                        $tmpdefault = '1'; 

                    //เคย
                    }else{ 
                        $tmpdefault = '0'; 
                    }

                    //INSERT Cars
                    $data = new Cars();
                    $data->cars_license = $request->cars_license;
                    $data->default = $tmpdefault;
                    $data->isActive = 1;
                    $data->province_ID = $request->province_ID;
                    $data->brand_ID = $request->brand_ID;
                    $data->created_by = $request->created_by;
                    $data->save(); 

                    $head = '1';

                    //VIEW Cars
                    $body = DB::table('cars')
                                ->join('brand','cars.brand_ID','=','brand.brand_ID')
                                ->join('province','cars.province_ID','=','province.province_ID')
                                ->where('created_by','=',$request->created_by)
                                ->where('isActive','1')
                                ->orderBy('default','desc')->get();
                    $error = null;
                }
                return $this->SuccessAPIv2($head,$body,$error);

            //Student
            }else if($table == 'student'){

                //INSERT Student
                $data = new Student();
                $data->student_firstname = $request->student_firstname;
                $data->student_lastname = $request->student_lastname;
                $data->student_nickname = $request->student_nickname;
                $data->student_school = $request->student_school;
                $data->isActive = '1';
                $data->created_by = $request->created_by;
                $data->save(); 

                // $body = DB::table($table)->select('*')
                                            // ->join('bus_status_details', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                            // ->where('student.created_by','=',$request->users_ID)->get();

                // $body = DB::table($table)->select('student.student_ID','student.student_nickname','student.student_school')
                                            // ->where(['student.created_by' => $request->users_ID])
                                            // ->join('bus_status_details', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                            // ->join('bus_status','bus_status_details.bus_status_ID', '=', 'bus_status.bus_status_ID')
                                            // ->join('cars','cars.cars_ID', '=', 'bus_status.cars_ID')
                                            // ->orderby('bus_status.bus_status_date','desc')
                                            // ->get();

                //SELECT นักเรียนที่ยังอยู่บนรถ
                $body1 = DB::table('bus_status_details')->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                                        ->join('bus_status', 'bus_status_details.bus_status_ID', '=', 'bus_status.bus_status_ID')
                                                        ->select('student.student_ID','student.student_nickname','student.student_school'
                                                                    ,'bus_status_details.bus_status_ID','bus_status_details.bus_status_details_ID'
                                                                    ,'bus_status_details.bus_status_details_in','bus_status_details.bus_status_details_out'
                                                                    ,'bus_status_details.isForgot'
                                                                )
                                                        ->where('student.created_by',$request->created_by)
                                                        ->where('bus_status_details.bus_status_details_out',null)
                                                        ->where('bus_status_details.isForgot',null)
                                                        ->where('student.isActive','1')
                                                        ->orderBy('bus_status.bus_status_in','asc')
                                                        ->get();

                //SELECT ลูกทั้งหมด
                $body2 = DB::table('student')->select('student.student_ID','student.student_nickname','student.student_school')
                                               // ->join('bus_status_details', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                                ->where('student.created_by',$request->created_by)
                                                // ->whereColumn('bus_status_details.bus_status_details_out')
                                                ->where('student.isActive','1')
                                                ->get();

                $head = '1';
                $body = array("active"=>$body1,"lists"=>$body2);       
                $error = null;

                return $this->SuccessAPIv2($head,$body,$error);

            }
        }
    }

    public function scanIn(Request $request)
    {
        /*
            Scan Get In
            - student_ID
            - cars_ID
            - created_by
        */

        //ตรวจสอบนักเรียน
        $findStu = DB::table('student')->where('student_ID',$request->student_ID)->count();

        if($findStu=='1'){

            //ตรวจสอบรายการใช้รถของวันนี้
            $findJob = DB::table('bus_status')
                                ->select('bus_status_ID')
                                ->where('created_by',$request->created_by)
                                ->WhereNull('bus_status_out')
                                ->WhereNull('isReset')
                                ->get();
                            
            //ยังไม่มีรายการใช้รถ
            if($findJob->count()=='0'){
                
                    //New Order
                    $NewJob = new Busstatus();
                    $NewJob->bus_status_date = date('Y-m-d');
                    $NewJob->cars_ID = $request->cars_ID;
                    $NewJob->bus_status_in = date('H:i');
                    $NewJob->created_by = $request->created_by;
                    $NewJob->save();
            
            //ตรวจสอบรายการใช้รถของวันนี้
            $findJob = DB::table('bus_status')
                                ->select('bus_status_ID')
                                ->where('created_by',$request->created_by)
                                ->WhereNull('bus_status_out')
                                ->WhereNull('isReset')
                                ->get();

                    $error = 'สร้างรายการใหม่';

            }else{

            }

            

            //ค้นหานักเรียนอยู่บนรถหรือไม่
            $findStudent = Busstatusdetails::where('student_ID',$request->student_ID)
                                                ->whereNull('bus_status_details_out')
                                                ->whereNull('isForgot')
                                                ->get();

            foreach ($findJob as $b) {
                    $bus_status_ID = $b->bus_status_ID;
            }

            //อยู่
            if($findStudent->count()=='0'){

                //Get In
                $body = new Busstatusdetails();
                $body->bus_status_ID = $bus_status_ID;
                $body->student_ID = $request->student_ID;
                $body->bus_status_details_in = date('H:i');
                $body->created_by = $request->created_by;
                $body->save();

                //ค้นจำนวนนักเรียนบนรถทั้งหมด
                $body = Busstatusdetails::where('bus_status_ID',$bus_status_ID)
                                            ->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                            ->orderby('bus_status_details_out','asc')->get();

                $error = 'นักเรียนขึ้นรถสำเร็จ';
                $head = '1';

            //ไม่ได้อยู่
            }else{

                foreach ($findJob as $b) {
                    $head = $b->bus_status_ID;
                }

                //ค้นจำนวนนักเรียนบนรถทั้งหมด
                $body = Busstatusdetails::where('bus_status_ID',$bus_status_ID)
                                            ->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                            ->orderby('bus_status_details_out','asc')->get();

                $error = 'นักเรียนคนนี้ขึ้นรถแล้ว';
                
            }

        }else{

            $head = '500';
            $body = [];
            $error = 'รูปแบบข้อมูลไม่ถูกต้อง';

        }
        
        return $this->SuccessAPIv2($head,$body,$error);

    }

    public function student(Request $request)
    {

        //INSERT Student
        $data = new Student();
        $data->student_firstname = $request->student_firstname;
        $data->student_lastname = $request->student_lastname;
        $data->student_nickname = $request->student_nickname;
        $data->student_school = $request->student_school;
        $data->isActive = '1';
        $data->created_by = $request->created_by;
        $data->save();  

        //SELECT นักเรียนที่ยังอยู่บนรถ
        $body1 = DB::table('bus_status_details')->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                                        ->join('bus_status', 'bus_status_details.bus_status_ID', '=', 'bus_status.bus_status_ID')
                                                        ->select('student.student_ID','student.student_nickname','student.student_school'
                                                                    ,'bus_status_details.bus_status_ID','bus_status_details.bus_status_details_ID'
                                                                    ,'bus_status_details.bus_status_details_in','bus_status_details.bus_status_details_out'
                                                                    ,'bus_status_details.isForgot'
                                                                )
                                                        ->where('student.created_by',$request->created_by)
                                                        ->where('bus_status_details.bus_status_details_out',null)
                                                        ->where('bus_status_details.isForgot',null)
                                                        ->where('student.isActive','1')
                                                        ->orderBy('bus_status.bus_status_in','asc')
                                                        ->get();

        //SELECT ลูกทั้งหมด
        $body2 = DB::table('student')->select('student.student_ID','student.student_nickname','student.student_school')
                                               // ->join('bus_status_details', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                                ->where('student.created_by',$request->created_by)
                                                // ->whereColumn('bus_status_details.bus_status_details_out')
                                                ->where('student.isActive','1')
                                                ->get();

        $body = array("active"=>$body1,"lists"=>$body2);       

        return $this->SuccessAPIv2('1',$body,'เพิ่มนักเรียนสำเร็จ');       
        
    }

    public function barcode(Request $request)
    {
    
        //INSERT Barcode
        $data = new Barcode();
        $data->text = $request->text;
        $data->save();  

        return $this->SuccessAPIv2('1',$data,'เพิ่มสำเร็จ');  

    }
        


}