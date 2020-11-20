<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Busstatusdetails extends Model
{
    protected $table = 'bus_status_details';
    protected $primaryKey = 'bus_status_details_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bus_status_details_ID','bus_status_ID','student_ID','bus_status_details_in','bus_status_details_out','isForgot','created_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    
    public function postDataUp(Request $request)
    {
        $this->validate($request, [
            'bus_status_ID' => 'required',
            'profile_ID' => 'required',
            'create_by' => 'required'
        ]);
    }




}