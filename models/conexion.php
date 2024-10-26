<?php

// class Conexion{

// 	static public function conectar(){

// 		    $link = new PDO("mysql:host=localhost;dbname=occu","root","");

// 			$acentos = $link->query("SET NAMES 'utf8'");

// 		return $link;
// 	}
// }

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
//loadEnv(__DIR__ . '/../../.env');

class Conexion{

    static public function conectar()
    {
        try {
            $host = getenv('DB_HOST');
            $name = getenv('DB_NAME');
            $user = getenv('DB_USER');
            $password = getenv('DB_PASS');
			

			$conn = new PDO("mysql:host=$host;dbname=$name", $user, $password);
            $conn->query("SET NAMES 'utf8'");
			//var_dump($host, $name, $user, $password);
            return $conn;
        } catch (PDOException $e) {
            echo "Conexión fallida: " . $e->getMessage();
        }

    }

}