<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Caballo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Mail\MailReserva;
use App\Mail\MailReservaEditada;
use App\Mail\MailReservaCancelada;
use Illuminate\Support\Facades\Mail;


class ApiController extends Controller
{    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password) && $user->email_verified_at !== null) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mensaje' => 'Logeado con éxito',
                'code' => '200'
            ]);

        } else if ($user === null) {
            return response()->json([
                'id' => 0,
                'name' => 'No encontrado',
                'email' => 'No encontrado',
                'mensaje' => 'Usuario no encontrado',
                'code' => '402'
            ]);

        } else if ($user->email_verified_at === null) {
            return response()->json([
                'id' => 0,
                'name' => 'No encontrado',
                'email' => 'No encontrado',
                'mensaje' => 'Debes verificar tu email. Revisa tu correo.',
                'code' => '402'
            ]);

        } else {
            return response()->json([
                'id' => 0,
                'name' => 'No encontrado',
                'email' => 'No encontrado',
                'mensaje' => 'Email o contraseña incorrectos',
                'code' => '402'
            ]);
        }
    }

    public function registrar(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);

        $usuarioRegistrado = User::where('email', $request->email)->exists();

        if ($usuarioRegistrado)
        {
            return response()->json([
                'id' => 0,
                'name' => 'No encontrado',
                'email' => 'No encontrado',
                'mensaje' => 'Email ya registrado',
                'code' => '402'
            ]);
        }

        try {
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $user = User::create($input);
            $result['token'] =  $user->createToken('MyAuthApp')->plainTextToken;
            $result['name'] =  $user->name;

            
            $user->sendEmailVerificationNotification();
            
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mensaje' => 'Registro completo',
                'code' => 200
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'id' => 'No encontrado',
                'name' => 'No encontrado',
                'email' => 'No encontrado',
                'mensaje' => 'Registro no completado',
                'code' => 402
            ]);
        }
    }

    public function getDatosUsuario($email)
    {
        $user = User::where('email', '=', $email)->first();

        if (!$user) {
            return response()->json([
                'id' => 1,
                'name' => 'No encontrado',
                'email' => 'No email'
            ], 404);
        }
        

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ], 200);
    }
    

    public function index()
    {
        $reservas = Reserva::all();
        return response()->json($reservas, 200);
    }

    public function misReservas($user_id)
    {
        $currentDate = Carbon::now();
        $reservas = Reserva::where('user_id', '=', $user_id)
            ->where('fecha', '>', $currentDate->toDateString())
            ->orderBy('fecha')
            ->orderBy('hora')
            ->get();

        $data = [];

        foreach($reservas as $reserva)
        {
            $caballo = Caballo::where('id', $reserva->id_caballo)->first();
            $nombreCaballo = $caballo ? $caballo->nombre : 'Sin caballo';
            $data[] =  [
                'id' => $reserva->id,
                'id_alumno' => $reserva->user_id,
                'id_caballo' => $reserva->id_caballo,
                'caballo' => $nombreCaballo,
                'fecha' => $reserva->fecha,
                'hora' => $reserva->hora,
                'comentarios' => $reserva->comentarios
            ];
        }

        return $data;
    }

    public function getListaCaballos()
    {
        $caballos = Caballo::where('enfermo', 0)->get();

        $data = [];

        foreach($caballos as $caballo)
        {
            $data[] = [
                'nombre' => $caballo->nombre,
            ];
        }

        return response()->json($data, 200);
    }

    public function storeReserva(Request $request)
    {
        $validatedData = $request->validate([
            'alumno' => 'required',
            'fecha' => 'required',
            'hora' => 'required',
            'caballo' => 'required',
            'comentarios' => 'nullable|string|max:255',
        ]);

        $mismaReserva = Reserva::where('user_id', $validatedData['alumno'])
            ->where('fecha', $validatedData['fecha'])
            ->where('hora', $validatedData['hora'])
            ->exists();

        $maximosAlumnos = Reserva::where('fecha', $validatedData['fecha'])
            ->where('hora', $validatedData['hora'])
            ->count();
            

        $caballo = Caballo::where('nombre', $validatedData['caballo'])->first();
        $caballoReservado = Reserva::where('id_caballo', $caballo->id)
            ->where('fecha', $validatedData['fecha'])
            ->where('hora', $validatedData['hora'])
            ->exists();

        $fechaSeleccionada = Carbon::createFromFormat('Y-m-d', $validatedData['fecha']);
        $fechaMaxima = Carbon::now()->addDays(30);

        if ($fechaSeleccionada->gt($fechaMaxima))
        {
            return response()->json(['mensaje' => 'No puedes reservar con más de 30 días de antelación',
                                        'code' => '402']);
        }
        else if ($mismaReserva)
        {
            return response()->json(['mensaje' => 'Ya tienes una reserva para esta fecha y hora',
                                        'code' => '402']);
        }
        else if ($maximosAlumnos >= 5)
        {
            return response()->json(['mensaje' => 'No quedan plazas libres para ese día',
                                        'code' => '402']);
        }
        else if ($caballoReservado)
        {
            return response()->json(['mensaje' => 'El caballo seleccionado está reservado',
                                        'code' => '402']);
        }
        else{
            $reserva = new Reserva();
            $reserva->fecha = $validatedData['fecha'];
            $reserva->hora = $validatedData['hora'];
            $reserva->user_id = $validatedData['alumno'];
            $reserva->id_caballo = $caballo->id;
            $reserva->comentarios = $validatedData['comentarios'];
            $reserva->save();

            $this->sendEmail($reserva);

            return response()->json(['mensaje' => 'Reserva guardada correctamente',
                                        'code' => '200']);
        }
    }

    public function editReserva(Request $request)
    {       
        $request->validate([
            'id_reserva_actual' => 'required',
            'alumno' => 'required',
            'fecha' => 'required',
            'hora' => 'required',
            'caballo' => 'required',
            'comentarios' => 'nullable|string|max:255',
        ]);
        
        $reservaActual = Reserva::where('id', $request->id_reserva_actual)->first();
        
        $mismaReserva = Reserva::where('user_id', $request->alumno)
            ->where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('id', '!=', $reservaActual->id)
            ->exists();

        $maximosAlumnos = Reserva::where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->count();
            

        $caballo = Caballo::where('nombre', $request->caballo)->first();
        
        $caballoReservado = Reserva::where('id_caballo', $caballo->id)
            ->where('fecha', $request->fecha)
            ->where('hora', $request->hora)
            ->where('id', '!=', $reservaActual->id)
            ->exists();

        $fechaSeleccionada = Carbon::createFromFormat('Y-m-d', $request->fecha);
        $fechaMaxima = Carbon::now()->addDays(30);

        if ($fechaSeleccionada->gt($fechaMaxima))
        {
            return response()->json(['mensaje' => 'No puedes reservar con más de 30 días de antelación',
                                        'code' => '402']);
        }
        else if ($mismaReserva)
        {
            return response()->json(['mensaje' => 'Ya tienes una reserva para esta fecha y hora',
                                        'code' => '402']);
        }
        else if ($maximosAlumnos >= 5)
        {
            return response()->json(['mensaje' => 'No quedan plazas libres para ese día',
                                        'code' => '403']);
        }
        else if ($caballoReservado)
        {
            return response()->json(['mensaje' => 'El caballo seleccionado está reservado',
                                        'code' => '402']);
        }
        else
        {
            $reservaActual->fecha = $request->fecha;
            $reservaActual->hora = $request->hora;
            $reservaActual->id_caballo = $caballo->id;
            $reservaActual->comentarios = $request->comentarios;
            $reservaActual->save();

            $this->sendEmailEditada($reservaActual);

            return response()->json(['mensaje' => 'Reserva actualizada correctamente',
                                        'code' => '200']);
        }
        
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id_reserva' => 'required',
        ]);

        $reserva = Reserva::where('id', $request->id_reserva)->first();
        $this->sendEmailCancelada($reserva);
        $reserva->delete();

        return response()->json(['mensaje' => 'Reserva borrada',
                                    'code' => '200']);
    }

    private function sendEmail($reserva)
    {
        $alumno = User::where('id', $reserva->user_id)->first();
        $caballo = Caballo::where('id', $reserva->id_caballo)->first();
        $detallesReserva = [
            'nombre' => $alumno->name,
            'caballo' => $caballo->nombre,
            'fecha' => $reserva->fecha,
            'hora' => \Carbon\Carbon::parse($reserva->hora)->format('H:i')
        ];

        Mail::to($alumno->email)->send(new MailReserva($detallesReserva));
    }

    private function sendEmailEditada($reserva)
    {
        $alumno = User::where('id', $reserva->user_id)->first();
        $caballo = Caballo::where('id', $reserva->id_caballo)->first();
        $detallesReserva = [
            'nombre' => $alumno->name,
            'caballo' => $caballo->nombre,
            'fecha' => $reserva->fecha,
            'hora' => \Carbon\Carbon::parse($reserva->hora)->format('H:i')
        ];

        Mail::to($alumno->email)->send(new MailReservaEditada($detallesReserva));
    }

    private function sendEmailCancelada($reserva)
    {
        $alumno = User::where('id', $reserva->user_id)->first();
        $detallesReserva = [
            'fecha' => $reserva->fecha,
            'hora' => \Carbon\Carbon::parse($reserva->hora)->format('H:i')
        ];

        Mail::to($alumno->email)->send(new MailReservaCancelada($detallesReserva));
    }
    

}
