<?php

class NewDoc
{
//vlastnosti classy
    private $id;
    private $nadpis;
    private $text = Null;


    public $error;
    public $success;
    private $storage = 'bin/dataFiles/data.json';
    private $stored_data;
    private $new_record;


//zde metody

    /**
     * @param $nadpis
     * @param $text
     */
    public function __construct($nadpis, $text)
    { // xss a uprava tady
        $this->nadpis = trim($this->nadpis);
        $this->nadpis = filter_var($nadpis, FILTER_SANITIZE_STRING);

        $this->text = filter_var($text, FILTER_SANITIZE_STRING);
        $this->text = trim($this->text);
        $this->stored_data = json_decode(file_get_contents($this->storage), true);

        $this->new_record = [
            'id' => uniqid(),
            'username' => $_SESSION['user'],
            'nadpis' => $this->nadpis,
            'obsah' => $this->text,
            'datum' => date('j.n.Y, H:i:s'),
            'photo' => Null,
            'photoid' => Null,
            'closed' => 0
        ];

        if ($this->ifNotEmpty()) { //vkladani uzivatele
            $this->insertDoc();
        }



    }

    /**
     * @return bool
     */
    private function ifNotEmpty(){
        if(empty($this -> text) || empty($this -> nadpis)){
            $this -> error = "Vyplň nadpis a detaily!";
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * @return bool
     */
    private function ifNotLarge(){
        if(strlen($this -> text) > 1000){ // musi byt mensi nez 1000
            $this -> error = "Příliš dlouhý popis! Zkrať to prosím.";
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * @return string
     */
    private function insertDoc(){
        if($this->ifNotLarge()) {
            array_push($this->stored_data, $this->new_record);
            if (file_put_contents($this->storage, json_encode($this->stored_data, JSON_PRETTY_PRINT))) {
                $this->success = "Přidání proběhlo v pořádku."; // pretty print nas zbavuje spagetti zapisu
                header('location: index.php');
                exit();
            } else {
                return $this->error = "Něco se nepovedlo, zkus to prosím znova!";
            }
        }
        else{
            return $this->error = "Příliš dlouhý popis! Zkrať to prosím.";  
        }
    }

}



