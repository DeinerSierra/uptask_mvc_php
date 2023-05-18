<?php
namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {
        session_start();
        isAuth();
        $id = $_SESSION['id'];
        $proyectos = Proyecto::belongsTo('propietarioId', $id);
        $router->render('dashboard/index',[
            'titulo'=>'Proyectos',
            'proyectos'=>$proyectos
        ]);

    }
    public static function crear_proyecto(Router $router) {
        session_start();
        $alertas = [];
        isAuth();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $proyecto = new Proyecto($_POST);
            //Validacion
            $alertas = $proyecto->validarProyecto();
            if (empty($alertas)) {
                //Generar una url unica
                $hash = md5(uniqid());
                $proyecto->url = $hash;
                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];
                # Guardar el proyecto
                $proyecto->guardar();
                //debuguear($proyecto);
                //Redireccionar
                header('Location: /proyectos?id='. $proyecto->url);

            }
            
        }
        $router->render('dashboard/crear-proyecto',[
            'titulo'=>'Crear Proyecto',
            'alertas'=>$alertas
        ]);
    }
    public static function proyecto(Router $router){
        session_start();
        isAuth();
        $token = $_GET['id'];
        if(!$token) header('Location: /dashboard');
        //Revisar que solo quien crea el proyecto pueda verlo
        $proyecto = Proyecto::where('url',$token);
        if($proyecto->propietarioId !== $_SESSION['id']) header('Location: /dashboard');

        $router->render('dashboard/proyecto',[
            'titulo'=>$proyecto->proyecto
        ]);

    }
    public static function perfil(Router $router) {
        session_start();
        isAuth();
        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validar_perfil();
            if (empty($alertas)) {
                $existeUsuario = Usuario::where('email',$usuario->email);
                if ($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    # Mostrar emnsaje de error
                    Usuario::setAlerta('error','Email no valido, ya hay un usuario con este email');
                    $alertas = $usuario->getAlertas();

                }else{
                    # Guardar el suario
                    $usuario->guardar();
                    Usuario::setAlerta('exito','Guardado Correctamente');
                    $alertas = $usuario->getAlertas();
                    $_SESSION['nombre'] = $usuario->nombre;

                }
                
            }
        }
        $router->render('dashboard/perfil',[
            'titulo'=>'Perfil',
            'alertas'=>$alertas,
            'usuario'=>$usuario
        ]);
    }
    public static function cambiar_password(Router $router){
        session_start();
        isAuth();
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $usuario = Usuario::find($_SESSION['id']);
            $usuario->sincronizar($_POST);
            $alertas = $usuario->nuevo_password();
            if (empty($alertas)) {
                $resultado = $usuario->comprobar_password();
                if ($resultado) {
                    # asignar el nuevo password.
                    $usuario->password = $usuario->nuevo_password;
                    //Eliminar propiedades del objeto no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->nuevo_password);
                    //hash al nuevo password
                    $usuario->hashPassword();
                    //Guardar la info en la db
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        Usuario::setAlerta('exito','Password actualizado correctamente');
                        $alertas = $usuario->getAlertas();
                    }

                }else{
                    Usuario::setAlerta('error','Password incorrecto');
                    $alertas = $usuario->getAlertas();
                }
            }
        }
        $router->render('dashboard/cambiar_password',[
            'titulo'=>'Cambiar Password',
            'alertas'=>$alertas,
            
        ]);

    }
}