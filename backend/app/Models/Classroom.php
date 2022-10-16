<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $table = 'classroom';

    protected $primaryKey = 'id_Classroom';

    protected $fillable = [
        'Number',
        'Pupil_capacity',
        'Musical_equipment',
        'Chemistry_equipment',
        'Computers',
        'fk_Floorid_Floor'
    ];

    protected $hidden = [
        'Musical_equipment',
        'Chemistry_equipment',
        'Computers'
    ];

    public $timestamps=false;

    public function lessons()
    {
        return $this->belongsToMany('App\Models\Lesson', 'fk_Classroomid_Classroom', 'id_Classroom');
    }

    public function floor()
    {
        return $this->hasOne('App\Models\Floor', 'id_Floor', 'fk_Floorid_Floor');
    }
}
