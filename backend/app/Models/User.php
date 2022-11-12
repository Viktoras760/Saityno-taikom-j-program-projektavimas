<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id_User';

    protected $fillable = [
        'Name',
        'Surname',
        'Personal_code',
        'email',
        'Grade',
        'password',
        'iat',
        'Confirmation',
        'fk_Schoolid_School'
    ];

    protected $hidden = [
        'iat',
        'fk_Schoolid_School',
        'remember_token'
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'Users id'              => $this->id_User,
            'Role'              => $this->Role,
        ];
    }

    public $timestamps=true;

    public function school()
    {
        return $this->hasOne('App\Models\School', 'id_School', 'fk_Schoolid_School');
    }

    public function lessons()
    {
        return $this->belongsToMany('App\Models\Lesson', 'user_lesson', 'fk_Userid_User', 'fk_Lessonid_Lesson');
    }
}
/*
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     
    protected $hidden = [
        'Password',
        'remember_token',
    ];

    public $timestamps=false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

*/