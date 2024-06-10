<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Caballo;

class CaballosController extends Controller
{

    public function index()
    {
        $listaCaballos = Caballo::get();
        return view('caballos.caballos', ['caballos' => $listaCaballos]);
    }

}
