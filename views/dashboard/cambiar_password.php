<?php include_once __DIR__.'/header-dashboard.php'; ?>
<div class="contenedor-sm">
    <?php include_once __DIR__.'/../templates/alertas.php'?>
    <a href="/perfil" class="enlace">Volver Al  Perfil</a>
    <form action="/cambiar_password" method="POST" class="formulario">
        <div class="campo">
            <label for="password_actual">Password Actual</label>
            <input type="password" name="password_actual" placeholder="Tu password actual">
        </div>
        <div class="campo">
            <label for="nuevo_password">Nuevo Password</label>
            <input type="password" name="nuevo_password" placeholder="Tu nuevo password">
        </div>
        <input type="submit" value="Guardar Cambios">
    </form>
</div>
<?php include_once __DIR__.'/footer-dashboard.php'; ?>