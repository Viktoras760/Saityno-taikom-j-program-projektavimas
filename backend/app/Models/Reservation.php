<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservation';

    protected $primaryKey = 'id_Reservation';

    protected $fillable = [
        'Lessons_name',
        'Reservation_period',
        'Lower_grade_limit',
        'Upper_grade_limit',
        'fk_Classroomid_Classroom',
        'fk_Userid_User'
    ];

    protected $hidden = [
        'fk_Classroomid_Classroom',
        'fk_Userid_User'
    ];

    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'id_Classroom', 'fk_Classroomid_Classroom');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id_User', 'fk_Userid_User');
    }
}
