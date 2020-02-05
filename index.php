<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

$app = new Slim();

$app->config('debug', true);

//beginning
$app->get('/', function() {

    $page = new Page(); //esta chamando o construct
    $page->setTpl("index");

});
//end

//beginning
$app->get('/admin', function() {

	User::verifyLogin(); //verificando se esta logado

    $page = new PageAdmin(); //esta chamando o construct
    $page->setTpl("index");


});
//end

//beginning
$app->get('/admin/login', function(){

	$page = new PageAdmin([
		"header" => false, //desabilitando do admin
		"footer" => false  //desabilitando do admin

	]); //esta chamando o login na pasta PageAdmin
	$page->setTpl("login");


});

$app->post('/admin/login', function() { //insert and save

  User::login($_POST["login"], $_POST["password"]); //metodo estatico
  
  header("Location: /admin");
  exit;

});

$app->get('/login/logout', function(){ //exit function

 User::logout();

 header("Location: /admin/login");
 exit;

});

$app->get("/admin/users", function(){ //Dando a rota

	User::verifyLogin(); //verificando se esta logado

	$users = User::listAll();

	$page = new PageAdmin();
	$page->setTpl("users", array(

		"users"=>$users
	));

});

$app->get("/admin/users/create", function(){ //Dando a rota

	User::verifyLogin(); //verificando se esta logado

	$page = new PageAdmin();
	$page->setTpl("users-create");

});

$app->get("/admin/users/:iduser/delete", function($iduser){//insert and save

	User::verifyLogin(); //verificando se esta logado


	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});

$app->get("/admin/users/:iduser", function($iduser){ //Dando a rota

	User::verifyLogin(); //verificando se esta logado

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();
	$page->setTpl("users-update", array(

		"user"=>$user->getValues()

	));

});

$app->post("/admin/users/create", function () {

 	User::verifyLogin();

	$user = new User();

 	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

 	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

 		"cost"=>12

 	]);

 	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
 	exit;

});

$app->post("/admin/users/:iduser", function($iduser){//insert and save

	User::verifyLogin(); //verificando se esta logado

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});


$app->run();

 ?>