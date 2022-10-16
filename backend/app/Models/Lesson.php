<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'lesson';

    protected $primaryKey = 'id_Lesson';

    protected $fillable = [
        'Lessons_name',
        'Lessons_starting_time',
        'Lessons_ending_time',
        'Lower_grade_limit',
        'Upper_grade_limit',
        'fk_Classroomid_Classroom',
        'fk_Userid_User'
    ];

    protected $hidden = [
        'fk_Classroomid_Classroom',
        'fk_Userid_User'
    ];

    public $timestamps=false;

    public function classroom()
    {
        return $this->hasOne('App\Models\Classroom', 'id_Classroom', 'fk_Classroomid_Classroom');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_lesson', 'fk_Lessonid_Lesson', 'fk_Userid_User');
    }
    /*public function users()
    {
        return $this->belongsToMany(User::class, 'user_lesson');
    }*/
}
