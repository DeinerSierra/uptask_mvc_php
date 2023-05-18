<?php
namespace Model;

class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','email','password','token','confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password_actual;
    public $nuevo_password;
    public $password2;
    public $token;
    public $confirmado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->nuevo_password = $args['nuevo_password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? '';
    }
    //Validar el login de usuarios
    public function validarLogin() {
        if (!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][]='El Email es invalido';
        }
        if (!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        return self::$alertas;

    }
    //Validacion para cuentas nuevas
    public function validarNuevaCuenta() {
        if (!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }
        if (!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }
        if (!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        if (strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password es debe tener mas de 6 caracteres';
        }
        if ($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los passwords no coinciden';
        }
        return self::$alertas;
    }
    //Comprobar el password
    public function comprobar_password() {
        return password_verify($this->password_actual,$this->password);
    }
    //hashear el password
    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }
    //Generar un token
    public function crearToken(){
        $this->token = uniqid();
    }
    //validar un email
    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][]='El Email es Obligatorio';
        }
        if (!filter_var($this->email,FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][]='El Email es invalido';
        }
        return self::$alertas;
    }
    //Validar password
    public function validarPassword() {
        if (!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }
        if (strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password es debe tener mas de 6 caracteres';
        }
        return self::$alertas;
    }
    //Validar perfil
    public function validar_perfil() {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre no puede ir vacio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El email no puede ir vacio';
        }
        return self::$alertas;
    }
    public function nuevo_password(){
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El password actual no puede ir vacio';
        }
        if(!$this->nuevo_password){
            self::$alertas['error'][] = 'El password nuevo no puede ir vacio';
        }
        if(strlen($this->nuevo_password)<6){
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }
}