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

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateController extends Controller
{

    public function update(Request $request)
    {
        $action = $request->action;
        $table = $request->table;
        
        //ตั้งค่าเริ่มต้น
        if($action == 'setDefault'){
            
            //รถ
            if($table == 'cars'){

                //ค้นหาและเปลี่ยนคันอื่นเป็นศูนย์
                $body = Cars::where('created_by','=',$request->created_by);
                $body->update(['default'=>0]);

                //ค้นหาและตั้งค่าเริ่มต้น
                $body = Cars::find($request->cars_ID);
                $body->update(['default'=>1]);

                //ค้นหารายชื่อรถ
                $body = DB::table('cars')
                                        ->join('brand','cars.brand_ID','=','brand.brand_ID')
                                        ->join('province','cars.province_ID','=','province.province_ID')
                                        ->where('created_by','=',$request->created_by)
                                        ->where('isActive','1')
                                        ->orderBy('default','desc')
                                        ->get();

                $error="แก้ไขข้อมูลสำเร็จ";
                $head = '1';

            
            }

            return $this->SuccessAPIv2($head,$body,$error);

        //แก้ไขข้อมูลส่วนตัว
        }else if($action == 'edit'){

            //Profile
            if($table=='profile'){

                $body = Profile::where('users_ID','=',$request->users_ID);
                $body->update([
                                'profile_firstname'=>$request->profile_firstname,
                                'profile_lastname'=>$request->profile_lastname,
                                'profile_mobile'=>$request->profile_mobile
                            ]);

                $body = DB::table('profile')
                            ->select('profile_firstname','profile_lastname','profile_mobile','roles_ID','users_ID','images_url','userId')
                            ->where('users_ID',$request->users_ID)
                            ->join('token', 'token.created_by', '=', 'profile.users_ID')
                            ->get();
                            
                $error = 'แก้ไขแล้ว';
                $head = '1';

            //Student
            }else if($table=='student'){

                $body = Student::where('student_ID','=',$request->student_ID);
                $body->update([
                                'student_firstname'=>$request->student_firstname,
                                'student_lastname'=>$request->student_lastname,
                                'student_nickname'=>$request->student_nickname,
                                'student_school'=>$request->student_school,
                            ]);

                $body = DB::table('student')
                            ->select('student_ID','student_firstname','student_lastname','student_nickname','student_school')
                            ->where('student_ID',$request->student_ID)
                            ->get();
                            
                $error = 'บุตรแก้ไขแล้ว';
                $head = '1';

            }

            return $this->SuccessAPIv2($head,$body,$error);

        }

    }

    public function scanOut(Request $request)
    {

        //ค้นหานักเรียน
        $findStu = DB::table('student')->where('student_ID',$request->student_ID)->count();

        //มีนักเรียนในระบบ
        if($findStu=='1'){

            //ค้นหานักเรียนอยู่บนรถหรือไม่
            $body = DB::table('bus_status_details')->where('student_ID',$request->student_ID)
                                                    ->where('bus_status_ID',$request->bus_status_ID)
                                                    ->whereNull('bus_status_details_out')
                                                    ->get();
            //มี
            if($body->count()=='1'){

                //หา Details ID นักเรียน
                $body = DB::table('bus_status_details')->where('student_ID','=',$request->student_ID)->get();
                foreach($body as $bb){
                    $bus_status_details_ID = $bb->bus_status_details_ID;
                }

                //นักเรียนลงรถ
                $body = DB::table('bus_status_details')->where('bus_status_details_ID','=',$bus_status_details_ID);
                $body->update(['bus_status_details_out'=>date('H:i')]);

                //ค้นจำนวนนักเรียนบนรถทั้งหมด
                $body = Busstatusdetails::where('bus_status_ID',$request->bus_status_ID)
                        ->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                        ->whereNull('bus_status_details.bus_status_details_out')
                        ->orderby('bus_status_details.bus_status_details_out','asc')->get();


                //ไม่มีนักเรียนอยู่บนรถ
                if($body->count()==0){

                    //จบภารกิจ
                    $body = DB::table('bus_status')->where('bus_status_ID','=',$request->bus_status_ID);
                    $body->update(['bus_status_out'=>date('H:i')]);

                    //ตรวจสอบครั้งสุดท้าย
                    $body = DB::table('bus_status')->where('bus_status_ID','=',$request->bus_status_ID)
                                                    ->whereNotNull('bus_status_out')->get();
                    if($body->count()==1){
                        $body = [];                    
                        $msg='การเดินทางสำเร็จ';
                        $head='200';
                    }

                //ยังมีนักเรียนอยู่บนรถ
                }else{

                    //ค้นจำนวนนักเรียนบนรถทั้งหมด
                    $body = Busstatusdetails::where('bus_status_ID',$request->bus_status_ID)
                                            ->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                            ->orderby('bus_status_details_out','asc')->get();

                    $msg='นักเรียนลงรถสำเร็จ';
                    $head='200';

                }

            //ไม่มี
            }else{

                $head='404';
                $body = [];
                $msg='นักเรียนลงไปแล้ว';

            }

        //ไม่มี
        }else{

            $head = '500';
            $body = [];
            $msg = 'รูปแบบข้อมูลไม่ถูกต้อง';

        }
        
        return $this->SuccessAPIv2($head,$body,$msg);

    }

    public function isReset(Request $request)
    {

         //ค้นหา
        //  $findForgot = DB::table('bus_status_details')->where('bus_status_ID',$request->bus_status_ID)->whereNull('isForgot')->count();  
         

        //กดปุ่มแล้ว
        // if($findForgot!='0'){

            // $head = '500';
            // $body = [];
            // $msg = 'กดอีกทำไม';

        //ยังไม่กด
        // }else{

            $body = DB::table('bus_status_details')->where('bus_status_ID','=',$request->bus_status_ID)->whereNull('bus_status_details_out');
            $body->update(['isForgot'=>1]);

            $body = DB::table('bus_status')->where('bus_status_ID',$request->bus_status_ID)->whereNull('isReset');  
            $body->update(['isReset'=>1]);

            $head = '200';
            $body = [];
            $msg = 'สำเร็จ';

        // }


        return $this->SuccessAPIv2($head,$body,$msg);

    }

}