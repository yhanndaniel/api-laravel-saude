<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'paciente';

    protected $guarded = [];

    protected function cpf(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => formatCPF($value),
            set: fn ($value) => formatOnlyNumber($value)
        );
    }

    protected function celular(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => formatPhone($value),
            set: fn ($value) => formatOnlyNumber($value)
        );
    }
}