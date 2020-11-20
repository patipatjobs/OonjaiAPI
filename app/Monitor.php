<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    protected $table = 'monitor';
    protected $primaryKey = 'monitor_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'monitor_ID','teacher_ID','driver_ID','register_ID'
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

            'teacher_ID' => 'required',
            'driver_ID' => 'required',
            'register_ID' => 'required'

        ]);
    }


}