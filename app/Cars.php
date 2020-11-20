<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cars extends Model
{
    protected $table = 'cars';
    protected $primaryKey = 'cars_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cars_ID','cars_license','default','isActive','province_ID','brand_ID','created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function postData(Request $request)
    {
        $this->validate($request, [
            'car_license' => 'required',
            'province_ID' => 'required',
            'brand_ID' => 'required',
            'create_by' => 'required'
        ]);
    }
    



}