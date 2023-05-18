<?php
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    public static function login (Router $router){
        //echo 'Desde Login';
        $alertas = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            if (empty($alertas)){
                //Verificar que el usuario exista
                $usuario = Usuario::where('email',$usuario->email);
                if (!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error','El usuario no existe o no esta confirmado');
                } else {
                    //el usuario si existe
                    if (password_verify($_POST['password'], $usuario->password)) {
                        //Iniciar la sesion del usuario.
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //Redireccionar
                        header('Location: /dashboard');
                        
                    }else {
                        Usuario::setAlerta('error','Password Incorrecto');
                    }
                }
            }

        }
        $alertas = Usuario::getAlertas();
        //Rebderizar la vista de login
        $router->render('auth/login',[
            'titulo'=>'Iniciar Sesion',
            'alertas'=>$alertas
        ]);
    }
    public static function logout (Router $router){
        //Iniciar la sesion para traer la informacion del usuario
        session_start();
        //Limpiamos la super global SESSION
        $_SESSION= [];
        //redireccionamos
        header('Location: /');
        
    }

    public static function crear (Router $router){
        $alertas = [];
        //Instanciar el modelo Usuario
        $usuario = new Usuario;
        //echo 'Desde crear';
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            //Validacion de campos
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            if (empty($alertas)) {
                //validar que el usuario no exista
                $existeUsuario = Usuario::where('email',$usuario->email);
                if ($existeUsuario){
                    Usuario::setAlerta('error','El email ya existe en el sistema');
                    $alertas = Usuario::getAlertas();
                } else {
                    //Crear un nuevo usuario
                    //hashear el password
                    $usuario->hashPassword();
                    //eliminar el pasword2
                    unset($usuario->password2);
                    //Generar el token
                    $usuario->crearToken();
                    //usuario confirmado
                    $usuario->confirmado = 0;
                    //Guardamos el usuario en la db
                    //Crear un redireccion dpendiendo el resultado
                    $resultado = $usuario->guardar();
                    //Enviar el email con el token
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    if($resultado){
                        header('Location: /mensaje');
                    }

                    debuguear($usuario);
                }
            }
            
        }
        //Rebderizar la vista de crear
        $router->render('auth/crear',[
            'titulo'=>'Crear tu cuenta en Uptask',
            'usuario'=>$usuario,
            'alertas'=>$alertas
        ]);
    }
    public static function olvide (Router $router){
        $alertas = [];
        //echo 'Desde olvide';
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $usuario = new Usuario($_POST);
            $usuario->validarEmail();
            if (empty($alertas)) {
                # Buscar el usuariocon el email obtyenido generar un token y enviarlo al correo
                $usuario = Usuario::where('email', $_POST['email']);//$usuario->email
                if ($usuario && $usuario->confirmado) {
                    # Si hay un usuario con ese correo encontre el usuario
                    //Generar el nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    //Actualizar el usuario
                    $usuario->guardar();
                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();
                    //Imprimir la alerta
                    Usuario::setAlerta('exito','Se ha enviado las intrucciones para el cambio a tu correo');
                    $alertas = Usuario::getAlertas();
                } else {
                    Usuario::setAlerta('error','Usuario no encontrado o no confirmado');
                    $alertas = Usuario::getAlertas();
                }
            }
        }
        //Rebderizar la vista de olvide
        $router->render('auth/olvide',[
            'titulo'=>'Olvide mi password',
            'alertas'=>$alertas
        ]);
    }
    public static function reestablecer (Router $router){
        $alertas = [];
        $motrar = true;
        $token = s($_GET['token']);
        if(!$token) header('Location: /login');
        //Indentificar el usuario con ese token
        $usuario = Usuario::where('token', $token);
        if (empty($usuario)) {
            Usuario::setAlerta('error','Token no valido');
            $motrar = false;
        }
        //echo 'Desde reestablecer';
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            # Añadir el nuevo password
            $usuario->sincronizar($_POST);
            //Validar el password.
            $alertas = $usuario->validarPassword();
            if (empty($alertas)) {
                #hashear el nuevo password
                $usuario->hashPassword();
                //elimnar el token
                $usuario->token = null;
                //Guardar el usuario
                $resultado = $usuario->guardar();
                //Redireccionar
                if ($resultado) {
                    header('Location: /');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        //Rebderizar la vista de reestablecer
        $router->render('auth/reestablcer',[
            'titulo'=>'Reestablecer Password',
            'alertas'=>$alertas,
            'mostrar'=>$motrar
        ]);
    }
    public static function mensaje (Router $router){
        //echo 'Desde mensaje';
        //Rebderizar la vista de mensaje
        $router->render('auth/mensaje',[
            'titulo'=>'Cuenta creada exitosamente'
        ]);
    }
    public static function confirmar (Router $router){
        //leer el token de la url
        $token = s($_GET['token']);
        if (!$token) {
            header('Location: /');
        }
        //encontrar al usuario con el token
        $usuario = Usuario::where('token',$token);
        if(empty($usuario)){
                //No se encontro ningun usuario con el token
            Usuario::setAlerta('error','Token invalido');
        }else {
            //Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = null;
            unset($usuario->password2);
            $usuario->guardar();
            Usuario::setAlerta('exito','Cuenta Comprobada Con exito');

        }
        $alertas = Usuario::getAlertas();
        //Renderizar la vista de confirmar
        $router->render('auth/confirmar',[
            'titulo'=>'Confirma tu cuenta UpTask',
            'alertas'=> $alertas
        ]);
        
    }
}