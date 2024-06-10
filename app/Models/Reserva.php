<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Orchid\Attachment\Attachable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Reserva extends Model
{
    use HasFactory;
    use AsSource, Filterable, Attachable;

    protected $table = 'reservas';
    protected $fillable = ['fecha', 'hora', 'comentarios'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function caballo()
    {
        return $this->belongsTo(Caballo::class, 'id_caballo');
        //return $this->belongsTo(Caballo::class);
    }
    
    public function getNombreCaballo()
    {
        $caballo = Caballo::find($this->id_caballo);
        return $caballo ? $caballo->nombre : 'Caballo no encontrado';
    }
}
