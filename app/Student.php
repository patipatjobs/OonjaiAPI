<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    protected $table = 'student';
    protected $primaryKey = 'student_ID';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'student_ID','student_firstname','student_lastname','student_nickname','student_school','isActive','created_by'
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
    //         'school_title' => 'required',
    //         'idcard' => 'required'
    //     ]);
    // }

}