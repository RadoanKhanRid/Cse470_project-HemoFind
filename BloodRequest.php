<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{


    use HasFactory;
    public function create()
{
    return view('blood_requests.create');
}

    // This is the "Passport" that lets data into your database
    protected $fillable = [
        'patient_name',
        'blood_type',
        'hospital_name',
        'required_date',
        'lat',
        'lng',
    ];
}