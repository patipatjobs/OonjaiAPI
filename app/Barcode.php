<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    protected $table = 'barcode';
    protected $primaryKey = 'id';
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','text','created_at','updated_at'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // public function postData(Request $request)
    // {
    //     $this->validate($request, [

    //         'profile_firstname' => 'required',
    //         'profile_lastname' => 'required',
    //         'profile_mobile' => 'required',
    //         'roles_ID' => 'required',
    //         'users_ID' => 'required'
    //     ]);
    // }



}