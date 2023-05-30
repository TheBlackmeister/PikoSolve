<?php
session_start();
class AddAnswer{
    // konstruktor

    private $stringid; // id prispevku
    public $error;
    public $success;
    private $stored_answers;

    // metody

    /**
     * @param $stringid
     * @param $odpoved
     */
    public function  __construct($stringid, $odpoved) { // zde xss, trim atd
        $this->stringid = $stringid;
        $this->odpoved = trim($odpoved);
        $this->odpoved = htmlspecialchars($odpoved);
        $this->stored_answers = file_get_contents('bin/dataFiles/answers.json');
        $this->stored_answers = json_decode($this->stored_answers, JSON_OBJECT_AS_ARRAY);
        $this->new_record = [
            'stringid' => $stringid, // urcuje k jakemu vláknu neboli příspěvku je vázaná
            'id' => uniqid(), // unikatni identifikace
            'username' => $_SESSION['user'], // kdo to pridal
            'obsah' => $this->odpoved, // text
            'datum' => date('j.n.Y, H:i:s'), // datum
            'isGOD' => $_SESSION['opravovatel']
        ];
        if($this->ifNotEmpty()) { //vkladani odpovedi
            $this->insertAnswer();
        }
    }

    /**
     * @return bool
     */
    private function ifNotEmpty(){ //validace dat
        if(empty($this -> odpoved)){
            $this -> error = "Napiš odpověď!";
            return false;
        }
        elseif (strlen($this->odpoved)>1000) {
            $this -> error = "Délka odpovědi je limitována na 1000 znaků!";
            return false;
        }
        else{
            return true;
        }
    }


    /**
     * @return string
     */
    private function insertAnswer(){
            array_push($this -> stored_answers, $this->new_record);
            if(file_put_contents('bin/dataFiles/answers.json', json_encode($this->stored_answers, JSON_PRETTY_PRINT))){
                return $this -> success = "Odeslání odpovědi proběhlo v pořádku.";
            }
            else {
                return $this->error = "Něco se nepovedlo, zkus to prosím znova!";
            }
        }


}