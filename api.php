<?php

/**
 * Punto de entrada de la API REST.
 * Inicializa sesión, conexión y dependencias para el controlador.
 */
session_start();
header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/app/Models/UserModel.php";
require_once __DIR__ . "/app/Models/ProductModel.php";
require_once __DIR__ . "/app/Models/SaleModel.php";
require_once __DIR__ . "/app/Controllers/ApiController.php";

$usuarios = new UserModel($conexion);
$productos = new ProductModel($conexion);
$ventas = new SaleModel($conexion);
$controlador = new ApiController($usuarios, $productos, $ventas);

$metodo = $_SERVER["REQUEST_METHOD"] ?? "GET";
$recurso = $_GET["recurso"] ?? "";
$accion = $_GET["accion"] ?? "";

$controlador->manejar($metodo, $recurso, $accion);
?>
