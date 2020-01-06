<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    //

    protected $fillable = [
        'full_name',
        'height',
        'weight',
        'breast_volume',
        'sex',
        'waist',
        'hips',
        'model_school_education',
        'about',
        'hobby',
        'education',
        'wish_learn',
        'age',
        'clothing_size',
        'shoe_size',
        'eye_color',
        'hair_color',


        'city',
    ];
}
