<?php

class NewDocWithPic
{
//vlastnosti classy
    private $id;
    private $nadpis;
    private $text = Null;
    private $photo;
    private $photoid;
    public $error;
    public $success;
    private $storage = 'bin/dataFiles/data.json';
    private $stored_data;
    private $new_record;


//zde metody

    /**
     * @param $nadpis
     * @param $text
     * @param $photo
     * @param $photoid
     */
    public function __construct($nadpis, $text, $photo, $photoid)
    { // zde je ve vsech tridach trim a htmlspecialchars
        $this->photo = $photo;
        $this->photoid = $photoid;
        $this->nadpis = trim($this->nadpis);
        $this->nadpis = filter_var($nadpis, FILTER_SANITIZE_STRING);
        $this->text = trim($this->text);
        $this->text = filter_var($text, FILTER_SANITIZE_STRING);

        $this->stored_data = json_decode(file_get_contents($this->storage), true);

        $this->new_record = [
            'id' => uniqid(),
            'username' => $_SESSION['user'],
            'nadpis' => $this->nadpis,
            'obsah' => $this->text,
            'datum' => date('j.n.Y, H:i:s'),
            'photo' => $this->photo,
            'photoid' => $this->photoid,
            'closed' => 0
        ];

        if ($this->ifNotEmpty()) { //vkladani uzivatele
            if($this->photoValidation()) {
                $this->insertDoc();
            }
        }
    }

    /**
     * @return bool
     */
    private function ifNotEmpty(){
        if(empty($this -> text) || empty($this -> nadpis)){
            $this -> error = "Vyplň nadpis a detaily!";
            $photopath = getcwd(). '/bin/dataFiles/pic/' .$this->photoid;
            unlink($photopath);
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
        if(strlen($this -> text) > 1000){ // mene jak tisic musi mit
            $this -> error = "Příliš dlouhý popis! Zkrať to prosím.";
            return false;
        }
        else{
            return true;
        }
    }

    /**
     * @return bool
     */
    private function photoValidation(){
        $photopath = getcwd(). '/bin/dataFiles/pic/' .$this->photoid; // getcwd je funkce vracici root directory pro linux
        if (!exif_imagetype($photopath)){ // jestli je fotka
            $this -> error = "Nahraný soubor není fotka!"; //odebrani souboru jako napriklad zip archivu a podobne
            unlink($photopath); // vymazat
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    private function insertDoc()
    {
        if ($this->ifNotLarge()) {
            array_push($this->stored_data, $this->new_record);
            if (file_put_contents($this->storage, json_encode($this->stored_data, JSON_PRETTY_PRINT))) {
                $this->success = "Přidání proběhlo v pořádku."; // pretty print nedela spagetti kod
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