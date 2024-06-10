<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Model\User;

use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Caballo extends Model
{
    use HasFactory;
    use AsSource, Filterable, Attachable;

    protected $table = 'caballos';
    protected $fillable = ['nombre', 'raza', 'fechaNacimiento', 'enfermo', 'observaciones'];

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }
}
