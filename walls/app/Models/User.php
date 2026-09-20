<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'point_of_sale_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_owner' => 'boolean',
            'can_view_analytics' => 'boolean',
        ];
    }

    /**
     * Точка продаж пользователя по умолчанию.
     */
    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class);
    }
}