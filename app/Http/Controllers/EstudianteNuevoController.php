<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estudiantes;
use App\Models\Subdominios;
use App\Models\Semestre;
use App\Models\MateriaEstudiante;
use App\Models\Personas;
use App\Models\UnidadAcademica;
use App\Models\Pensum;
use Illuminate\Support\Facades\DB;

class EstudianteNuevoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $this->validate($request,[
                'nombre_estudiante' => 'required|min:2|max:50',
                'paterno_estudiante' => 'required|min:2|max:50',
                'materno_estudiante' => 'required|min:2|max:50',
                'numero_ci_estudiante' => 'required|min:5',
                'extension_ci_estudiante' => 'required',
                'fec_nacimiento_estudiante' => 'required',
                'numero_telefono_estudiante' => 'required|min:8|max:11',
                'correo_personal_estudiante' => 'required|min:8|max:50',
                'domicilio_estudiante' => 'required|min:5|max:100',
                'genero_estudiante' => 'required',
                'nombre_tutor' => 'required|min:10|max:150',
                'telefono_tutor' => 'required|min:8|max:11',
                'domicilio_tutor' => 'required|max:100',
                'anio_estudiante' => 'required',
                'ua_estudiante' => 'required'
            ]);
            $mateC = new MateriaEstudiante;
            $personaE = Personas::select('personas.*')
                        ->where('personas.per_num_documentacion', '=', $request->get('numero_ci_estudiante'))
                        ->get()->first();
            $personaE->per_nombres = (string) $request->get('nombre_estudiante');
            $personaE->per_paterno = (string) $request->get('paterno_estudiante');
            $personaE->per_materno = (string) $request->get('materno_estudiante');
            $personaE->per_num_documentacion = trim($request->get('numero_ci_estudiante'));
            $personaE->per_subd_extension = $request->get('extension_ci_estudiante');
            $personaE->per_fecha_nacimiento = $request->get('fec_nacimiento_estudiante');
            $personaE->per_telefono = (int) $request->get('numero_telefono_estudiante');
            $personaE->name = $request->get('nombre_estudiante').' '.$request->get('paterno_estudiante').' '. $request->get('materno_estudiante');
            $personaE->email = (string) $request->get('correo_personal_estudiante');
            $personaE->per_domicilio = (string) $request->get('domicilio_estudiante');
            $personaE->per_subd_genero = $request->get('genero_estudiante');
            $personaE->per_ua_id = $request->get('ua_estudiante');
            $personaE->per_rol = 3;

            $estudianteE = Estudiantes::select('estudiantes.*')
                            ->where('estudiantes.est_per_id', '=', $personaE->per_id)
                            ->get()->first();

            $estudianteE->est_sem_id = $request->get('anio_estudiante');
            $estudianteE->est_subd_estado = 7;
            $estudianteE->est_nombre_tutor =  $request->get('nombre_tutor');
            $estudianteE->est_telefono_tutor =  $request->get('telefono_tutor');
            $estudianteE->est_domicilio_tutor =  $request->get('domicilio_tutor');
            $estudianteE->est_ocupacion_tutor =  $request->get('ocupacion_tutor');
            $estudianteE->est_bachiller =  $request->get('bachiller');
            $estudianteE->est_cert_nac =  $request->get('nacimiento');
            $estudianteE->est_fot_ci =  $request->get('ciEst');
            $estudianteE->est_fot_tutor =  $request->get('ciTutor');
            $estudianteE->est_certificaciones =  $request->get('certificaciones');
            $estudianteE->est_experiencia =  $request->get('experiencia');
            $estudianteE->est_examen_ingreso_estado =  13;
            $personaE->save();
            $estudianteE->save();
            return redirect()->route('administracion.index')->with('status', 'Se completo el registro del estudiante.');
        } catch(Exception $e){
            return view('ebid-views-administrador.home')->with('status', 'Hubo un error inusual');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $personas = Personas::find($id);
            if($personas == null){
                $datos = DB::table('personas')
                    ->join('estudiantes', 'estudiantes.est_per_id', '=', 'personas.per_id')
                    ->where('estudiantes.est_id', '=', $id)
                    ->where('estudiantes.est_subd_estado', '=', 8)
                    ->get()
                    ->first();
            }
            else if($personas != null){
                $datos = DB::table('personas')
                    ->join('estudiantes', 'estudiantes.est_per_id', '=', 'personas.per_id')
                    ->where('personas.per_id', '=', $id)
                    ->where('estudiantes.est_per_id', '=', $id)
                    ->get()
                    ->first();
            }
            
            if($datos!= null){
                $nombreExt = Subdominios::select('subdominios.*')
                        ->where('subd_id', '=', $datos->per_subd_extension)
                        ->get()->first();
                $nombreGenero = Subdominios::select('subdominios.*')
                                ->where('subd_id', '=', $datos->per_subd_genero)
                                ->get()->first();
                $genero = Subdominios::select('subdominios.*')
                        ->where('subd_dom_id', '=', 2)
                        ->get();
                $extension = Subdominios::select('subdominios.*')
                        ->where('subd_dom_id', '=', 9)
                        ->get();
                $anio = Semestre::select('semestre.*')
                        ->get();
                $ua = UnidadAcademica::select('unidad_academica.*')
                        ->get();
                return view('ebid-views-administrador.estudiante.estudiante-nuevo', [
                    'datos'=>$datos, 
                    'genero'=>$genero, 
                    'extension'=>$extension, 
                    'anio'=>$anio, 'uacad'=>$ua, 'nombreExt'=>$nombreExt,
                    'nombreGen'=>$nombreGenero]);
            }else if($datos == null){
                return redirect()->route('administracion.index')->with('status', 'No se encontro ningun registro correspondiente a su usuario');
            }
            
        } catch(Exception $e){
            return view('ebid-views-administrador.home')->with('status', 'Hubo un error inusual');
        }
    }


    public function edit($id)
    {
        dd("Reprobo");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
