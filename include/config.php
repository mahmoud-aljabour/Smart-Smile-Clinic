<?php
defined('server') ? null : define("server", "localhost");
defined('user') ? null : define("user", "root");
defined('pass') ? null : define("pass", "");
defined('database_name') ? null : define("database_name", "db-dental-07-10");
// db-dentalclinic

// $web_root =  "https://mcguyverja.com/invoice/"; 
$web_root = "http://localhost:8080/DentalClinic/";


define('web_root', $web_root);
define('app_name', 'Smart Smile Clinic');
define('app_tagline', 'A complete clinic management platform, appointments, patient records, and billing in one secure workspace.');
define('app_logo', web_root . 'dist/img/Smart_Smile_Clinic_logo_v2.svg');
