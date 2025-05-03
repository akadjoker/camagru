<?php
session_start();
require_once 'config/database.php';
require_once 'core/Router.php';
$page = $_GET['page'] ?? 'home';
Router::route($page);
