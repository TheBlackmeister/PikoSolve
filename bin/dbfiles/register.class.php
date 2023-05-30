<?php require("login.class.php") ?>
<?php
class RegisterUser {
    //vlastnosti classy
    private $id;
    private $username;
    private $raw_password;
    private $encrypted_password;
    private $name;
    private $surname;
    private $sex;
    private $ZS;
    private $school;
    private $pikomatsolver;
    private $email;

    public $error;
    public $success;
    private $storage = 'bin/dbfiles/users.json';
    private $stored_users;
    private $new_user;


    //zde metody

    /**
     * @param $username
     * @param $password
     * @param $name
     * @param $surname
     * @param $sex
     * @param $ZS
     * @param $school
     * @param $pikomatsolver
     * @param $email
     */
    public function __construct($username, $password, $name, $surname, $sex, $ZS, $school, $pikomatsolver, $email){

        $this->email = trim($this->email);
        $this->email = filter_var($email, FILTER_SANITIZE_STRING);

        $this -> username = trim($this -> username);
        $this -> username = filter_var($username, FILTER_SANITIZE_STRING);

        $this -> raw_password = filter_var(trim($password), FILTER_SANITIZE_STRING);
        $this -> encrypted_password = password_hash($this->raw_password, PASSWORD_DEFAULT);

        $this -> name = trim($this -> name);
        $this -> name = filter_var($name, FILTER_SANITIZE_STRING);

        $this -> school = trim($this -> school);
        $this -> school = filter_var($school, FILTER_SANITIZE_STRING);

        $this -> surname = trim($this -> surname);
        $this -> surname = filter_var($surname, FILTER_SANITIZE_STRING);

        $this -> sex = trim($this -> surname);
        $this -> sex = filter_var($sex, FILTER_SANITIZE_STRING);

        $this -> ZS = trim($this -> ZS);
        $this -> ZS = filter_var($ZS, FILTER_SANITIZE_STRING);

        $this -> pikomatsolver = trim($this -> pikomatsolver);
        $this -> pikomatsolver = filter_var($pikomatsolver, FILTER_SANITIZE_STRING);

        $this -> stored_users = json_decode(file_get_contents($this -> storage), true);

        $this -> new_user = [
            'id' => uniqid(),
            'username' => $this -> username,
            'password' => $this -> encrypted_password,
            'name' => $this -> name,
            'surname' => $this -> surname,
            'sex' => $this -> sex,
            'ZS' => $this -> ZS,
            'school' => $this -> school,
            'pikomatsolver' => $this -> pikomatsolver,
            'email' => $this->email,
            'admin' => 0,
            'opravovatel' => 0,
            'datum' => date('j.n.Y, H:i:s')
        ];

        if($this->ifNotEmpty()) { //vkladani uzivatele
                $this->insertUser();
                $user = new LoginUser($this->username, $this->raw_password);
        }
    }

    /**
     * @return bool
     */
    private function ifNotEmpty(){ //validace dat
        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
        if(empty($this -> username) || empty($this -> raw_password) || empty($this -> name) || empty($this -> surname) || empty($this -> sex) || empty($this -> ZS) || empty($this -> pikomatsolver)){
            $this -> error = "Vyplň kolonky s hvězdičkou!";
            return false;
            } // empty
        elseif (strlen($this->username)<5) {
            $this -> error = "Uživatelské jméno musí být delší než 4 znaky!";
            return false;
        } // delka
        elseif (strlen($this->raw_password)<8) {
            $this -> error = "heslo musí být delší než 7 znaků!";
            return false;
        } // delka
        elseif (preg_match('/[\'"^£$%&*()}{@#~?><,|=_+¬-]/', $this-> username)) {
            $this -> error = "Zakázané znaky ve jménu, vymaž je!";
            return false;
        } // spec znaky
        elseif (preg_match('/[\'"^£$%&*()}{@#~?><,|=_+¬-]/', $this-> name)) {
            $this->error = "Vymaž speciální znaky ze svého jména!";
            return false;
        } // spec znaky
        elseif (preg_match('/[\'"^£$%&*()}{@#~?><,|=_+¬-]/', $this-> surname)) {
            $this->error = "Vymaž speciální znaky ze svého příjmení!";
            return false;
        } // spec znaky
        elseif(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $this->email)){
            $this->error = 'Špatný formát emailové adresy!';
            return false;
        } // spec znaky email
        return true;
    }

    /**
     * @return bool
     */
    private function usernameExists(){
        foreach($this -> stored_users as $user) {
            if($this -> username == $user['username']) {
                $this -> error = "Zvol prosím jiné uživatelské jméno, toto je již použito!";
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    private function emailExists(){
        foreach($this -> stored_users as $user) {
            if($this -> email == $user['email']) {
                $this -> error = "Tento email je již používán! Pokud jsi zapomněl heslo, napiš adminovi, email najdeš na stránce 'O nás', rád ti pomůže.";
                return true;
            }
        }
        return false;


    }


    /**
     * @return string|void
     */
    private function insertUser(){
        if($this -> usernameExists() == false) {
            if ($this->emailExists() == false) {
                array_push($this->stored_users, $this->new_user);
                if (file_put_contents($this->storage, json_encode($this->stored_users, JSON_PRETTY_PRINT))) {
                    return $this->success = "Registrace proběhla v pořádku.";
                } else {
                    return $this->error = "Něco se nepovedlo, zkus to prosím znova!";
                }
            }
            else{
                return $this->error = "Tento email je již používán! Pokud jsi zapomněl heslo, napiš adminovi, email najdeš na stránce 'O nás', rád ti pomůže.";
            }
        }
    }
}


