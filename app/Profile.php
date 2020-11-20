<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profile';
    protected $primaryKey = 'profile_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'profile_ID','profile_prefix','profile_firstname','profile_lastname','profile_mobile','users_ID','roles_ID','images_url'
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

            'profile_firstname' => 'required',
            'profile_lastname' => 'required',
            'profile_mobile' => 'required',
            'roles_ID' => 'required',
            'users_ID' => 'required'
        ]);
    }



}