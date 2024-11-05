<?php

use Src\Database\SqlConnection;
use Src\Models\Model;

class User extends Model {

    public function register() {

        $sql = new SqlConnection();

        $sql->select("CALL sp_register_user(:name, :last_name, :username, :email, :password)", [
            ":name"=>$this->getname(),
            ":last_name"=>$this->getlast_name(),
            ":username"=>$this->getusername(),
            ":email"=>$this->getemail(),
            ":password"=>$this->getpassoword(),
        ]);

    }


}