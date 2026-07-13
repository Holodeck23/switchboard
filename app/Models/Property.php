<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'timezone', 'wifi_network', 'access_notes'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
