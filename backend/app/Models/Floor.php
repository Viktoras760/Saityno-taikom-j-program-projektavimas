<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    protected $table = 'floor';

    protected $primaryKey = 'id_Floor';

    protected $fillable = [
        'Classroom_amount',
        'Sport_equipment',
        'fk_Schoolid_School'
    ];

    protected $hidden = [
        'Sport_equipment',
        'fk_Schoolid_School'
    ];

    public $timestamps=false;

    public function classrooms()
    {
        return $this->belongsToMany('App\Models\Classroom', 'fk_Floorid_Floor', 'fk_Classroomid_Classroom');
    }

    public function school()
    {
        return $this->hasOne('App\Models\School', 'id_School', 'fk_Schoolid_School');
    }
}
