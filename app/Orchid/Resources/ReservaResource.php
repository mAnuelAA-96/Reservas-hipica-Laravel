<?php

namespace App\Orchid\Resources;

use Orchid\Crud\Resource;
use Orchid\Screen\TD;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;
use Carbon\Carbon;
use App\Models\Caballo;
use App\Models\User;

class ReservaResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Reserva::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [

            Select::make('user_id')
                ->title('Alumno')
                ->options(User::pluck('name', 'id')->toArray())
                ->required(),

            Input::make('fecha')
                ->title('Fecha')
                ->type('date')
                ->required(),

            Select::make('hora')
            ->title('Hora')
            ->options([
                '10:00' => '10:00',
                '11:00' => '11:00',
                '12:00' => '12:00',
                '13:00' => '13:00',
            ])
            ->required(),

            Select::make('id_caballo')
                ->title('Caballo')
                ->options(Caballo::where('enfermo', 0)->pluck('nombre', 'id')->toArray())
                ->required(),

            Input::make('comentarios')
                ->title('Comentarios')
                ->placeholder('Comentarios')
        ];
    }

    /**
     * Get the columns displayed by the resource.
     *
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('id'),
            TD::make('user_id', 'Alumno')
                ->render(function ($model) {
                    return $model->user ? $model->user->name : "N/A";
                }),
            TD::make('id_caballo', 'Caballo')
                ->render(function ($model) {
                    return $model->caballo ? $model->caballo->nombre : "N/A";
                }),
            TD::make('fecha', 'Fecha'),
            TD::make('hora', 'Hora')
                ->render(function ($model) {
                    return Carbon::parse($model->hora)->format('H:i');
                }),
            TD::make('comentarios', 'Comentarios'),

            /*
            TD::make('created_at', 'Date of creation')
                ->render(function ($model) {
                    return $model->created_at->toDateTimeString();
                }),

            TD::make('updated_at', 'Update date')
                ->render(function ($model) {
                    return $model->updated_at->toDateTimeString();
                }),
            */
        ];
    }

    /**
     * Get the sights displayed by the resource.
     *
     * @return Sight[]
     */
    public function legend(): array
    {
        return [
            Sight::make('id', 'ID'),
            Sight::make('user_id', 'Alumno')
                ->render(function ($model) {
                    return $model->user ? $model->user->name : 'N/A';
                }),
            Sight::make('id_caballo', 'Caballo')
                ->render(function ($model) {
                    return $model->caballo ? $model->caballo->nombre : 'N/A';
                }),
            Sight::make('fecha', 'Fecha'),
            Sight::make('hora', 'Hora')
            ->render(function ($model) {
                return Carbon::parse($model->hora)->format('H:i');
            }),
            Sight::make('comentarios', 'Comentarios'),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array
     */
    public function filters(): array
    {
        return [];
    }
}
