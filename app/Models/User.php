<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
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
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_changed_at',
        'state'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function persona()
    {
        return $this->hasOne(Persona::class, 'usuario_id');
    }
    
    public function getRolId()
    {
        return $this->persona?->last_ap?->id_rol;
    }
    
    public function getRolName()
    {
        return $this->persona?->last_ap?->rol->name;
    }

    public function getAP()
    {
        return $this->persona?->last_ap;
    }

    // Que roles acceden
    public function hasAnyRoles(array $roles)
    {
        return in_array($this->getRolId(), $roles);
    }
}
