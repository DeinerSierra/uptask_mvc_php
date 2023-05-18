<div class="contenedor login">
    <?php
        include_once __DIR__ . '/../templates/nombre-sitio.php';
    ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesion</p>
        <?php
        include_once __DIR__ . '/../templates/alertas.php';
        ?>
        <form method="POST" action="/" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu email">
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Tu password">
            </div>

            <input type="submit" class="boton" value="Iniciar Sesion">
        </form>

        <div class="acciones">
            <a href="/crear">Aun No tienes una cuenta? click aqui</a>
            <a href="/olvide">Olvidaste tu password? click aqui</a>
        </div>
    </div>
</div>