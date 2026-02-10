<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable, TwoFactorAuthenticatable;
    use Notifiable; 

    protected $table = 'ost_staff';
    protected $primaryKey = 'staff_id';

    public $incrementing = false; 

    //  protected $keyType = 'int'; 

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'firstname',
        'lastname',
        'email',
        'password',
        'dni',
        'dept_id',
        'id_empresa',
    ];

    public function scopeActive($query)
    {
        return $query->where('activo', 1);
    }


    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id');
    }

    public function charge()
    {
        return $this->belongsTo(Charge::class, 'id_cargo');
    }

    // protected $hidden = [
    //     'password',
    // ];

    protected $appends = ['full_name'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
    //         'email_verified_at' => 'datetime',
    //         'password' => 'hashed',
    //         'two_factor_confirmed_at' => 'datetime',
    //     ];
    // }
}


//   `staff_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
//   `dept_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `role_id` int(10) unsigned NOT NULL DEFAULT 0,
//   `username` varchar(32) NOT NULL DEFAULT '',
//   `firstname` varchar(32) DEFAULT NULL,
//   `lastname` varchar(32) DEFAULT NULL,
//   `passwd` varchar(128) DEFAULT NULL,
//   `password` varchar(128) DEFAULT NULL,
//   `backend` varchar(32) DEFAULT NULL,
//   `email` varchar(255) DEFAULT NULL,
//   `phone` varchar(24) NOT NULL DEFAULT '',
//   `phone_ext` varchar(6) DEFAULT NULL,
//   `mobile` varchar(24) NOT NULL DEFAULT '',
//   `signature` text NOT NULL,
//   `lang` varchar(16) DEFAULT NULL,
//   `timezone` varchar(64) DEFAULT NULL,
//   `locale` varchar(16) DEFAULT NULL,
//   `notes` text DEFAULT NULL,
//   `isactive` tinyint(1) NOT NULL DEFAULT 1,
//   `isadmin` tinyint(1) NOT NULL DEFAULT 0,
//   `isvisible` tinyint(1) unsigned NOT NULL DEFAULT 1,
//   `onvacation` tinyint(1) unsigned NOT NULL DEFAULT 0,
//   `assigned_only` tinyint(1) unsigned NOT NULL DEFAULT 0,
//   `show_assigned_tickets` tinyint(1) unsigned NOT NULL DEFAULT 0,
//   `change_passwd` tinyint(1) unsigned NOT NULL DEFAULT 0,
//   `max_page_size` int(11) unsigned NOT NULL DEFAULT 0,
//   `auto_refresh_rate` int(10) unsigned NOT NULL DEFAULT 0,
//   `default_signature_type` enum('none','mine','dept') NOT NULL DEFAULT 'none',
//   `default_paper_size` enum('Letter','Legal','Ledger','A4','A3') NOT NULL DEFAULT 'Letter',
//   `extra` text DEFAULT NULL,
//   `permissions` text DEFAULT NULL,
//   `created` datetime NOT NULL,
//   `lastlogin` datetime DEFAULT NULL,
//   `passwdreset` datetime DEFAULT NULL,
//   `updated` datetime NOT NULL,
//   `dni` varchar(20) DEFAULT NULL,
//   `direccion` varchar(700) DEFAULT NULL,
//   `id_departamento` int(11) DEFAULT NULL,
//   `id_provincia` int(11) DEFAULT NULL,
//   `id_distrito` int(11) DEFAULT NULL,
//   `zona_tecnico` varchar(200) DEFAULT NULL,
//   `id_empresa` int(11) DEFAULT NULL,
//   `fecha_ingreso` date DEFAULT NULL,
//   `id_cargo` int(11) unsigned DEFAULT NULL,
//   `id_area` int(11) unsigned DEFAULT NULL,
//   `activo` tinyint(1) NOT NULL DEFAULT 1,