<?php

namespace App\Orchid\Resources;

use Orchid\Crud\Resource;
use Orchid\Screen\TD;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Sight;

class CaballoResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Caballo::class;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array
     */
    public function fields(): array
    {
        return [
            Input::make('nombre')
                ->title('Nombre')
                ->placeholder('Nombre')
                ->required(),
                
            Input::make('raza')
            ->title('Raza')
            ->placeholder('Raza')
            ->required(),

            Input::make('fecha_nacimiento')
                ->title('Fecha de nacimiento')
                ->type('date')
                ->required(),

            Select::make('enfermo')
                ->title('Enfermo')
                ->options([
                    0 => 'No',
                    1 => 'Si',
                ])
                ->required(),
            
            Input::make('observaciones')
                ->title('Observaciones')
                ->placeholder('Observaciones'),
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
            TD::make('nombre', 'Nombre'),
            TD::make('raza', 'Raza'),
            TD::make('fecha_nacimiento', 'Fecha de nacimiento'),
            TD::make('enfermo', 'Enfermo')
                ->render(function ($model) {
                    if ($model->enfermo == 0) {
                        return 'No';
                    } else {
                        return 'Si';
                    }
                }),
            TD::make('observaciones', 'Observaciones')
                ->render(function ($model) {
                    return substr($model->observaciones, 0, 20) . '...';
                }),
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
            Sight::make('nombre', 'Nombre'),
            Sight::make('raza', 'Raza'),
            Sight::make('fecha_nacimiento', 'Fecha de nacimiento'),
            Sight::make('enfermo', 'Enfermo')
                ->render(function ($model) {
                    if ($model->enfermo == 0) {
                        return 'No';
                    } else {
                        return 'Si';
                    }
                }),
            Sight::make('observaciones', 'Observaciones'),
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
