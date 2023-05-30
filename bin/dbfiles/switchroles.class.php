<?php

class RoleSwitch
{
    private $storage = 'bin/dbfiles/users.json';
    private $stored_users;
    public $error;
    public $success;
    private $flag = TRUE;

    /**
     * @param $method
     * @param $id
     */
    public function __construct($method, $id) {
        $this->method = $method;
        $this->id = $id;

        $this -> stored_users = json_decode(file_get_contents($this -> storage), JSON_OBJECT_AS_ARRAY);

        $this->switchRole();
    }


    /**
     * @return string|void
     */
    private function switchRole() {


        if (empty($this->stored_users)){
            return $this -> error = "Něcoasdfasfd se nepovedlo!.";
        }
        switch($this->method){
            case 'addAdmin':
                foreach ($this->stored_users as $key=>$user) {
                    if ($user['id'] == $this->id) {
                        $user[$key]['admin'] = 1;
                        $this->stored_users[$key]['admin'] = 1;
                        $this->flag = FALSE;
                    }

                }
                if ($this->flag == TRUE){
                    return $this -> error = "Něco se nepovedlo!.";
                }
                $this->stored_users = array_values($this->stored_users);
                file_put_contents('bin/dbfiles/users.json', json_encode($this->stored_users, JSON_PRETTY_PRINT));
                return $this -> success = "Změna proběhla v pořádku.";

            case 'deleteAdmin':
                foreach ($this->stored_users as $key=>$user) {
                    if ($user['id'] == $this->id) {
                        $user[$key]['admin'] = 0;
                        $this->stored_users[$key]['admin'] = 0;
                        $this->flag = FALSE;
                    }

                }
                if ($this->flag == TRUE){
                    return $this -> error = "Něco se nepovedlo!.";
                }

                $this->stored_users = array_values($this->stored_users);
                file_put_contents('bin/dbfiles/users.json', json_encode($this->stored_users, JSON_PRETTY_PRINT));
                return $this -> success = "Změna proběhla v pořádku.";

            case 'addOrg':
                foreach ($this->stored_users as $key=>$user) {
                    if ($user['id'] == $this->id) {
                        $user[$key]['opravovatel'] = 1;
                        $this->stored_users[$key]['opravovatel'] = 1;
                        $this->flag = FALSE;
                    }

                }
                if ($this->flag == TRUE){
                    return $this -> error = "Něco se nepovedlo!.";
                }

                $this->stored_users = array_values($this->stored_users);
                file_put_contents('bin/dbfiles/users.json', json_encode($this->stored_users, JSON_PRETTY_PRINT));
                return $this -> success = "Změna proběhla v pořádku.";

            case 'deleteOrg':
                foreach ($this->stored_users as $key=>$user) {
                    if ($user['id'] == $this->id) {
                        $user[$key]['opravovatel'] = 0;
                        $this->stored_users[$key]['opravovatel'] = 0;
                        $this->flag = FALSE;
                    }

                }
                if ($this->flag == TRUE){
                    return $this -> error = "Něco se nepovedlo!.";
                }

                $this->stored_users = array_values($this->stored_users);
                file_put_contents('bin/dbfiles/users.json', json_encode($this->stored_users, JSON_PRETTY_PRINT));
                return $this -> success = "Změna proběhla v pořádku.";

        }
   }


}
