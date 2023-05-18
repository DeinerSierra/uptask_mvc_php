<div class="contenedor crear">
    <?php
        include_once __DIR__ . '/../templates/nombre-sitio.php';
    ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crea Tu Cuenta En  UpTask</p>
        <?php
        include_once __DIR__ . '/../templates/alertas.php';
        ?>
        <form method="POST" action="/crear" class="formulario">
             <div class="campo">
                <label for="nombre">Nombre</label></label>
                <input type="text" name="nombre" id="nombre" placeholder="Tu nombre" value="<?php echo $usuario->nombre;?>">
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu email" value="<?php echo $usuario->email;?>">
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Tu password">
            </div>
            <div class="campo">
                <label for="password2">Confirmar Password</label>
                <input type="password2" name="password2" id="password2" placeholder="Confirma tu password">
            </div>

            <input type="submit" class="boton" value="Crear Cuenta">
        </form>

        <div class="acciones">
            <a href="/">ya tienes una cuenta? click aqui</a>
            <a href="/olvide">Olvidaste tu password? click aqui</a>
        </div>
    </div>
</div>