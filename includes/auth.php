<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function verificarSesion()
{
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ../index.php');
        exit;
    }
}

function verificarRol($roles)
{
    if (!in_array($_SESSION['usuario_rol'], $roles)) {
        header('Location: dashboard.php');
        exit;
    }
}

function esAdmin()
{
    return $_SESSION['usuario_rol'] === 'admin';
}

function esMedico()
{
    return $_SESSION['usuario_rol'] === 'medico';
}
