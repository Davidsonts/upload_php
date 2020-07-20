<?php
ini_set('display_errors', '0'); // 1 OR 0
ini_set('display_startup_errors', '0'); // 1 OR 0
error_reporting(0); // E_ALL OR 0

include_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// var_dump(getenv('USER_NAME'));
// var_dump($_ENV['USER_NAME']);

$connection = "host=".$_ENV['HOST_NAME']." port=".$_ENV['PORT']." dbname=".$_ENV['DB_NAME']." user=".$_ENV['USER_NAME']." password=".$_ENV['PASSWORD'];
$db = pg_connect($connection);

if (!$connection) {
    $error = error_get_last();
    echo "Connection failed. Error was: ". $error['message']. "\n";
} else {
    echo "Connection succesful.\n";
}

$dir = $_ENV['DIR'];
$upload_dir = $_ENV['UPLOAD_DIR'];

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$files = scandir($dir);
$path = pathinfo(__FILE__);

// print_r($dir);
// echo PHP_EOL;
// print_r($upload_dir);
// echo PHP_EOL;
// print_r($files);
// echo PHP_EOL;
// print_r($path);
// echo PHP_EOL;
// print_r($path['dirname']);
// echo PHP_EOL;


foreach ($files as $key => $file) {

    if(is_file($file) == true && exif_imagetype($file)){

        $ext = substr($file, strrpos($file, '.') + 1);
        $name_new = time().uniqid(md5()).'.'.$ext;

        if(copy($path['dirname'].'/'.$file, $upload_dir.$name_new)){
                $array = ['name_old' => $file, 'name_new' => $name_new,  'local_old' => $path['dirname'], 'local_new' => $upload_dir, ];

                $res = pg_insert($db, 'images', $array);

                if ($res) {
                    echo "Dados POST arquivados com sucesso\n";
                }
                else {
                    echo "O usuário deve ter inserido entradas inválidas\n";
                }
        }else{
            echo "Erro ao Copiar";
        }

    }
    
}

// CREATE DATABASE 
// CREATE TABLE images (
//     id serial PRIMARY KEY,
//     name_old TEXT NOT NULL,
//     name_new TEXT NOT NULL,
//     local_old TEXT NOT NULL,
//     local_new TEXT NOT NULL,
//     created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
// );

?>
