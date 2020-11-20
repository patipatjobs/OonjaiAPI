<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Busstatus extends Model
{
    protected $table = 'bus_status';
    protected $primaryKey = 'bus_status_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bus_status_ID','bus_status_date','cars_ID','bus_status_in','bus_status_out','isReset','created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'bus_status_lat',
        'bus_status_lng',
        'bus_status_map_time'
    ];

    // public function postData(Request $request)
    // {
    //     $this->validate($request, [
    //         'car_license' => 'required',
    //         'province_ID' => 'required',
    //         'brand_ID' => 'required',
    //         'create_by' => 'required',
    //         'images_id' => null
    //     ]);
    // }



}