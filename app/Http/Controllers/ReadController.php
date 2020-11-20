<?php

namespace App\Http\Controllers;

use DB;

use Profile;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class ReadController extends Controller
{

    public function read(Request $request)
    {
        $action = $request->action;
        $table = $request->table;

        //Dropdownlist
        if($action=='lists'){

            //Brand
            if($table=='brand'){
  
                $body = DB::table('brand')->orderBy('brand_title', 'asc')->get();
                $error =null;
                $head = $body->count();
       
            //Province
            }else if($table=='province'){

                $body = DB::table('province')->orderByRaw(DB::raw("FIELD(province_ID, 10) DESC"))->orderBy('province_name_th', 'asc')->get(); 
                $error =null;
                $head = $body->count();
               
            }else{
                $head = '0';
                $body = [];
                $error = "ไม่ให้เข้า";
            }

            return $this->SuccessAPIv2($head,$body,$error);

        //Check
        }else if($action=='check'){

            if($table == 'profile'){

                    $body = DB::table('profile')
                                ->join('token','profile.users_ID','=','token.created_by')
                                ->select('profile.profile_ID','profile.users_ID', 'profile.profile_mobile','profile.profile_firstname','profile.profile_lastname','profile.roles_ID','profile.images_url','token.userId')
                                ->orWhere('profile.profile_mobile',$request->profile_mobile)
                                ->orWhere('profile.profile_ID',$request->profile_ID)
                                ->orWhere('profile.users_ID',$request->users_ID)
                                ->orWhere('token.userId',$request->userId)
                                ->get();
                    
                    $error = null;
                    $head = $body->count();

            }else{
                $error = 'ไม่มีข้อมูล';
                $body = [];
                $head = '0';
            }

            return $this->SuccessAPIv2($head,$body,$error);
        
        //SHOW
        }else if($action=='show'){

            if($table=='busstatus'){

                $body = DB::table('bus_status')->where('bus_status.bus_status_ID',$request->bus_status_ID)
                                                            ->join('bus_status_details','bus_status_details.bus_status_ID','=','bus_status.bus_status_ID')
                                                            ->get();

            //
            }else if($table=='busstatusdetails'){

                $body = DB::table('bus_status_details')->where('bus_status_details_ID',$request->bus_status_details_ID)->get();
            
            //
            }else if($table=='profile'){

                //
                if(!empty($request->users_ID)){
                    $body = DB::table($table)->where('users_ID',$request->users_ID)
                                                // ->join('profile', 'profile.profile_ID', '=', 'users.users_ID')
                                                ->get();
                
                //
                }else if(!empty($request->profile_ID)){
                    $body = DB::table($table)->where('profile_ID',$request->profile_ID)
                                                // ->join('profile', 'profile.profile_ID', '=', 'users.users_ID')
                                                ->get();
                }

            //
            }else if($table=='student'){

                //
                if(!empty($request->student_ID)){

                    $body = DB::table($table)->where('student_ID',$request->student_ID)->get();  

                //
                }else if(!empty($request->created_by)){

                    $body = DB::table($table)->where('created_by',$request->created_by)->get();  

                }
            
            //
            }else if($table=='cars'){

                $body = DB::table($table)->join('brand','cars.brand_ID','=','brand.brand_ID')
                                            ->join('province','cars.province_ID','=','province.province_ID')
                                            ->orWhere('cars.cars_ID',$request->cars_ID)
                                            ->orWhere('cars.created_by',$request->created_by)
                                            ->orderby('cars.default','desc')
                                            ->orderby('cars.isActive','desc')
                                            ->get();

            }

            $error = null;
            return $this->SuccessAPIv2($body->count(),$body,$error);

        }
        // else if($action=='history'){

            //CARS HISTORY
        //     if($table=='cars'){

        //         //
        //         $body1 = DB::table('cars')
        //         // ->where('bus_status.bus_status_ID',$request->bus_status_ID)
        //         // ->where('cars.cars_ID',$request->cars_ID)
        //         ->where('cars.created_by',$request->created_by)
        //         ->where('isActive',1)
        //         ->orderby('default','desc')
        //         ->get();

        //         //
        //         $body2 = DB::table('cars')
        //                     ->select('bus_status.bus_status_ID','cars.cars_ID','bus_status.bus_status_date','bus_status.bus_status_in','bus_status.bus_status_out','bus_status.bus_status_date','bus_status_details.student_ID','bus_status_details.isForgot')
        //                     ->join('bus_status','bus_status.cars_ID','=','cars.cars_ID')
        //                     ->join('bus_status_details','bus_status_details.bus_status_ID','=','bus_status.bus_status_ID')
        //                     // ->where('bus_status.bus_status_ID',$request->bus_status_ID)
        //                     // ->where('cars.cars_ID',$request->cars_ID)
        //                     ->where('cars.created_by',$request->created_by)
        //                     ->where('bus_status.bus_status_out','!=',null)
        //                     ->where('bus_status.isReset','=',null)
        //                     ->where('isActive',1)
        //                     ->orderby('default','desc')
        //                     ->get();

        //         //
        //         // $body3 = DB::table('bus_status_details')
        //         //             ->where('created_by',$request->created_by)
        //         //            // ->where('bus_status_ID',$body2->bus_status_ID)
        //         //             ->whereNull('bus_status_details_out')
        //         //             ->whereNull('isForgot')
        //         //             ->get();

        //         $error = null;
        //         $body = array("cars"=>$body1,"lists"=>$body2); 
        //         return $this->SuccessAPIv2('1',$body,$error);
        //     }

        // //
        // }
        else{

            printf("Error");

        }

    }
    
    public function home(Request $request)
    {
                //พนักงานขับรถ
                if($request->roles_ID=='1'){

                    //ค้นหาการเดินทางปัจจุบัน
                    $body = DB::table('bus_status')
                                ->join('cars', 'cars.cars_ID', '=', 'bus_status.cars_ID')
                                ->join('province', 'cars.province_ID', '=', 'province.province_ID')
                                // ->whereDate('bus_status.bus_status_date', Carbon::today())
                                ->whereNull('bus_status.bus_status_out')
                                ->whereNull('bus_status.isReset')
                                ->where('cars.isActive',1)
                                ->where('bus_status.created_by','=',$request->created_by)
                                ->first();

                    //ตรวจสอบว่ามีการเดินทางอยู่หรือไม่
                    if(!empty($body->bus_status_ID)){

                        $body2 = DB::table('bus_status_details')
                                    ->join('student', 'bus_status_details.student_ID', '=', 'student.student_ID')
                                    ->where('bus_status_details.created_by','=',$request->created_by)
                                    ->where('bus_status_details.bus_status_ID','=',$body->bus_status_ID)
                                    ->orderby('bus_status_details.bus_status_details_out','asc')
                                    ->get();
                        $body = array("title"=>$body,"details"=>$body2); 
                        $error = "มีรายการ";
                        
                    //ไม่มีการเดินทาง
                    }else{

                        $body2 = DB::table('student')->whereNull('student_lastname')->get();
                        $body = array("title"=>$body,"details"=>$body2); 
                        $error = 'หน้าหลักไม่มีรายการ';

                    }
                            
                    

                //ผู้ปกครอง
                }else if($request->roles_ID=='2'){

                    //ค้นหาการเดินทางปัจจุบัน
                    // $body = DB::table('bus_status_details')
                    //             ->join('student','bus_status_details.student_ID','=','student.student_ID')
                    //             ->join('bus_status','bus_status.bus_status_ID','=','bus_status_details.bus_status_ID')
                    //             ->whereDate('bus_status.bus_status_date', Carbon::today())
                    //             ->where('student.created_by','=',$request->created_by)
                    //             ->get();

                                $body = DB::table('bus_status')
                                ->join('bus_status_details','bus_status.bus_status_ID','=','bus_status_details.bus_status_ID')
                                ->join('student','bus_status_details.student_ID','=','student.student_ID')
                                ->whereDate('bus_status.bus_status_date', Carbon::today())
                                ->whereNull('bus_status.bus_status_out')
                                ->whereNull('bus_status.isReset')
                                ->whereNull('bus_status_details.bus_status_details_out')
                                ->whereNull('bus_status_details.isForgot')
                                ->where('student.isActive',1)
                                ->where('student.created_by','=',$request->created_by)
                                ->get();

                    $error = 'หน้าหลัก';
                                
                }  
                
                return $this->SuccessAPIv2('200',$body,$error);

    }

    public function cars(Request $request)
    {

        //ค้นหารถ
        $body = DB::table('cars')->join('province', 'cars.province_ID', '=', 'province.province_ID')
                                    ->join('brand','cars.brand_ID','=','brand.brand_ID')
                                    ->where('isActive',1)->where('cars.created_by',$request->created_by)
                                    ->orderBy('default','desc')->get();

        return $this->SuccessAPIv2($body->count(),$body,'รายการรถ');

    }
    
    public function student(Request $request)
    {

        //ค้นหาจาก ID นักเรียน
        if(!empty($request->student_ID)){

            //ค้นหานักเรียน
            $body = DB::table('student')
                        ->where('student_ID',$request->student_ID)
                        ->where('isActive',1)
                        ->get();  
            $head = $body->count();

        //ค้นหาจาก ID แม่
        }else if(!empty($request->created_by)){

            //ค้นหาสถานะของนักเรียน        
            $head = DB::table('bus_status_details')
                        ->join('bus_status','bus_status_details.bus_status_ID','=','bus_status.bus_status_ID')    
                        ->join('student','bus_status_details.student_ID','=','student.student_ID')  
                        ->select('bus_status_details.student_ID')
                        ->where('bus_status_details.bus_status_details_out',null)
                        ->where('bus_status_details.isForgot',null)
                        ->where('bus_status.isReset',null)  
                        ->where('student.created_by',$request->created_by)
                        ->get();

            //หานักเรียน
            $body = DB::table('student')
                        // ->join('student','bus_status_details.student_ID','=','student.student_ID')
                        ->select('student.student_ID','student.student_nickname','student.student_school')
                        ->where('student.created_by',$request->created_by)
                        ->where('student.isActive','1')
                        ->get();

        }

        return $this->SuccessAPIv2($head,$body,'รายการบุตร'); 

    }

    public function history(Request $request)
    {
  
        $start = date("Y-m-d",strtotime($request->input('from_date')));
        $end = date("Y-m-d",strtotime($request->input('to_date')."-14 day"));

        //พนักงานขับรถ
        if($request->roles_ID=='1'){

            //View Lists 2 Weeks ago
            $body = DB::table('bus_status')
                        ->select('bus_status_ID','bus_status_date','bus_status_in','bus_status_out','isReset')
                        ->whereNotBetween('bus_status_date', [$start,$end])
                        ->where('bus_status.cars_ID',$request->cars_ID)
                        ->where(function ($query) {
                            $query->where('bus_status_out', '!=', null)
                                  ->orWhere('isReset', '!=', null);
                        })
                        ->orderby('bus_status.bus_status_date','desc')
                        ->get();


            //             //View Lists 2 Weeks
            // $body = DB::table('bus_status_details')
            //             ->join('bus_status','bus_status.bus_status_ID', '=', 'bus_status_details.bus_status_ID')
            //             ->where('bus_status.created_by',$request->created_by)
            //             ->where('bus_status.cars_ID',$request->cars_ID)
            //             ->orderby('bus_status.bus_status_date','desc')
            //             ->get();

            

        //ผู้ปกครอง
        }else if($request->roles_ID=='2'){

            //View Lists 2 Weeks ago
            $body = DB::table('student')
                        ->select('student.student_ID','cars.cars_ID','bus_status.bus_status_date','cars.cars_license','bus_status_details.bus_status_details_in','bus_status_details.bus_status_details_out','bus_status_details.isForgot')
                        ->join('bus_status_details','bus_status_details.student_ID', '=', 'student.student_ID')
                        ->join('bus_status','bus_status.bus_status_ID', '=', 'bus_status_details.bus_status_ID')
                        ->join('cars','cars.cars_ID', '=', 'bus_status.cars_ID')   
                        ->where('student.student_ID',$request->student_ID)
                        ->whereNotBetween('bus_status.bus_status_date', [$start,$end])   
                        ->orderby('bus_status.bus_status_date','desc')
                        ->orderby('bus_status_details.bus_status_details_in','desc')               
                        ->get();

            //View MyStudent
            // $body1 = DB::table('student')
            //             ->where('student.created_by',$request->created_by)
            //             ->where('isActive',1)
            //             ->get();

            // foreach ($body1 as $b) {
            //     $student_ID = $b->student_ID;
            // }

            // $msg = "View Student";
            // $body = array("cars"=>$body1,"lists"=>[]); 
            // return $this->SuccessAPIv2($body1->count(),$body,$msg);   
        }

        return $this->SuccessAPIv2($body->count(),$body,'รายการประวัติย้อนหลังสองอาทิตย์');  
        
    }

    public function details(Request $request)
    {
        //พนักงานขับรถ
        if($request->roles_ID=='1'){
            if(isset($request->bus_status_ID)){

                $body = DB::table('bus_status_details')
                            ->select('bus_status.bus_status_date','student.student_nickname','student.student_school','bus_status_details.bus_status_details_in','bus_status_details.bus_status_details_out','bus_status_details.isForgot')
                            ->join('student','student.student_ID', '=', 'bus_status_details.student_ID')
                            ->join('bus_status','bus_status.bus_status_ID', '=', 'bus_status_details.bus_status_ID')
                            ->Where('bus_status_details.bus_status_ID',$request->bus_status_ID)
                            ->orderby('bus_status_details.bus_status_details_in','desc')
                            ->get();

            }

        //ผู้ปกครอง
        }else if($request->roles_ID=='2'){

            $body = DB::table('profile')
                        ->select('profile.profile_firstname','profile.profile_lastname','brand.brand_title','cars.cars_license','province.province_name_th','profile.profile_mobile','profile.images_url')
                        ->join('cars','cars.created_by', '=', 'profile.users_ID')
                        ->join('province','province.province_ID', '=', 'cars.province_ID')
                        ->join('brand','brand.brand_ID', '=', 'cars.brand_ID')
                        ->Where('cars.cars_ID',$request->cars_ID)
                        ->get();

        }

        return $this->SuccessAPIv2($body->count(),$body,'รายละเอียด');  

    }

}

        // if($request->roles_ID==1){
        //     $data = DB::table('profile')
        //                 ->join('cars', 'cars.created_by', '=', 'profile.users_ID')
        //                 ->join('brand', 'brand.brand_ID', '=', 'cars.brand_ID')
        //                 ->where("users_ID",$request->users_ID)
        //                 ->where("roles_ID","=","1")
        //                 ->get(); 
        // }else if($request->roles_ID==2){
        //     $data = DB::table('profile')
        //                 ->where("users_ID",$request->users_ID)
        //                 ->where("roles_ID","=","2")
        //                 ->get(); 
        // }else if($request->roles_ID==3){
        //     $data = DB::table('profile')
        //                 ->where("users_ID",$request->users_ID)
        //                 ->where("roles_ID","=","3")
        //                 ->get(); 
        // }else if($request->roles_ID==4){
        //     $data = DB::table('profile')
        //                 ->where("users_ID",$request->users_ID)
        //                 ->where("roles_ID","=","4")
        //                 ->get(); 
        // }

        // $count = $data->count();
        // $status = 1;

        // return $this->SuccessFull($status,$count,$data);
    

    // public function description($who, Request $request)
    // {  
    //     if($who=='driver'){

    //     }

    //     $count = $data->count();
    //     $status = 1;

    //     return $this->SuccessFull($status,$count,$data);
    // }
                    // if(!empty($request->userId)){

                //     $body = DB::table('token')->join('profile','profile.users_ID','=','token.created_by')
                //     ->select('profile.profile_ID','profile.users_ID', 'profile.profile_mobile','profile.profile_firstname','profile.profile_lastname','profile.roles_ID','profile.images_url','token.userId')
                //                                 ->where('token.userId',$request->userId)->get();

                //}