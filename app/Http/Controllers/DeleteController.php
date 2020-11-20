<?php

namespace App\Http\Controllers;

use DB;

use App\Users;
use App\Profile;
use App\Token;
use App\Cars;
use App\Student;
use App\Busstatus;
use App\Busstatusdetials;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DeleteController extends Controller
{

    public function delete(Request $request)
    {
        $action = $request->action;
        $table = $request->table;

        //Cars
        if($table == 'cars'){

            //รถคันนี้เคยใช้งานหรือไม่
            $body = DB::table('bus_status')->where('cars_ID',$request->cars_ID)->get();

            //ไม่เคย
            if($body->count()==0){

                //ค้นหาและลบ
                $body = Cars::find($request->cars_ID);
                $body->delete();
                $error="ลบข้อมูลสำเร็จ";

            //เคย
            }else{

                //ค้นหาและเปลี่ยนสถานะเป็นไม่ใช้งาน
                $body = Cars::find($request->cars_ID);
                $body->update(['isActive'=>0]);
                $error = "ระงับการใช้งานสำเร็จ";

            }
 
            $head ='1';

            //ค้นหารถ
            $body = DB::table('cars')->join('brand','cars.brand_ID','=','brand.brand_ID')
                                        ->join('province','cars.province_ID','=','province.province_ID')
                                        ->where('created_by','=',$request->created_by)
                                        ->where('isActive','1')
                                        ->orderBy('default','desc')
                                        ->get();

            return $this->SuccessAPIv2($head,$body,$error);

        //Student
        }else if($table == 'student'){

            //นักเรียนเคยมีประวัติหรือไม่
            $body = DB::table('bus_status_details')->where('student_ID',$request->student_ID)->get();

            //ไม่เคย
            if($body->count()==0){

                //ค้นหาและลบ
                $body = Student::find($request->student_ID);
                $body->delete();
                $error="ลบข้อมูลสำเร็จ";

            //เคย
            }else{

                //ค้นหาและเปลี่ยนสถานะเป็นไม่ใช้งาน
                $body = Student::find($request->student_ID);
                $body->update(['isActive'=>0,'default'=>0]);
                $error = "ระงับการใช้งานสำเร็จ";

            }
 
            $head ='1';
            $body = DB::table('student')->where('created_by','=',$request->created_by)->where('isActive','1')->get();

            return $this->SuccessAPIv2($head,$body,$error);

        }

    }

}