<div class="contenedor olvide">
    <?php
        include_once __DIR__ . '/../templates/nombre-sitio.php';
    ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">recupera tu acceso UpTask</p>
        <?php
        include_once __DIR__ . '/../templates/alertas.php';
        ?>

        <form method="POST" action="/olvide" class="formulario">
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Tu email">
            </div>

            <input type="submit" class="boton" value="Enviar Instrucciones">
        </form>

        <div class="acciones">
            <a href="/">ya tienes una cuenta? click aqui</a>
            <a href="/crear">Aun No tienes una cuenta? click aqui</a>
        </div>
    </div>
</div>