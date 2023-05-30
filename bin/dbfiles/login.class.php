<?php

class LoginUser{


    // konstruktor
    private $username;
    private $password;
    public $error;
    public $success;
    /**
     * @var string
     */
    private $storage = "bin/dbfiles/users.json";
    private $stored_users;

    // metody

    /**
     * @param $username
     * @param $password
     */
    public function  __construct($username, $password) {
        $this->username = trim(htmlspecialchars($username));
        $this->password = $password;
        $this->stored_users = json_decode(file_get_contents($this->storage), JSON_OBJECT_AS_ARRAY);
        $this->login();
    }

    /**
     * @return string|void
     */
    private function login(){
        foreach ($this->stored_users as $user) {
            if($user['username'] == $this->username){
                if(password_verify($this->password, $user['password'])){
                    //session_start(); // diky csrf zacina uz na form.php
                    $_SESSION['user'] = $this->username;
                    $_SESSION['opravovatel'] = $user['opravovatel'];
                    $_SESSION['admin'] = $user['admin'];
                    header('location: index.php');
                    exit();
                }
            }
        }
        return $this->error = "Špatně zadané jméno, nebo heslo, zkus to znova.";
    }

}