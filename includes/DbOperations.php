<?php

    class DbOperations{
        //the database connection variable
        public $con;

        //inside constructor
        //we are getting the connection link
        function __construct(){
            require_once dirname(__FILE__) . '/DbConnect.php';
            $db = new DbConnect;
            $this->con = $db->connect();
        }


        /*  The Create Operation
            The function will insert a new user in our database
        */
        public function createUser($email, $password, $name, $school){
           if($this->isEmailExist($email)){
                $stmt = $this->con->prepare("INSERT INTO `users`  (`email`, `password`, `name`, `school`) VALUES (?, ?, ?, ?)");
                $task=  $stmt->execute(array($email, $password, $name, $school));
                if($task){
                    return USER_CREATED;
                }else{
                    return USER_FAILURE;
                }
           }else{
           return USER_EXISTS;
         }
        }


        /*
            The Read Operation
            The function will check if we have the user in database
            and the password matches with the given or not
            to authenticate the user accordingly
        */
        public function userLogin($email, $password){
            if($this->isEmailExist($email)){
                $hashed_password = $this->getUsersPasswordByEmail($email);
                if(password_verify($password, $hashed_password)){
                    return USER_AUTHENTICATED;
                }else{
                    return USER_PASSWORD_DO_NOT_MATCH;
                }
            }else{
                return USER_NOT_FOUND;
            }
        }

        /*
            The method is returning the password of a given user
            to verify the given password is correct or not
        */
        public function getUsersPasswordByEmail($email){
            $stmt = $this->con->prepare("SELECT `password` FROM `users` WHERE `email` = ?");

            $stmt->execute(array($email));

            $a=$stmt->fetch(PDO::FETCH_ASSOC);
            return $a['password'];
        }

        /*
            The Read Operation
            Function is returning all the users from database
        */
        public function getAllUsers(){
            $stmt = $this->con->prepare("SELECT `id`, `email`, `name`, `school` FROM `users`");
            $stmt->execute();

            $users = array();
            while($uss=$stmt->fetch(PDO::FETCH_ASSOC)){
                $user = array();
                $user['id'] = $uss['id'];
                $user['email']=$uss['email'];
                $user['name'] = $uss['name'];
                $user['school'] = $uss['school'];
                array_push($users, $user);
            }
            return $users;
        }

        /*
            The Read Operation
            This function reads a specified user from database
        */
        public function getUserByEmail($email){
            $stmt = $this->con->prepare("SELECT `id`, `email`, `name`, `school` FROM `users` WHERE `email` = ?");

            $stmt->execute(array($email));

            $stmt->fetch(PDO::FETCH_ASSOC);
            $user = array();
            $user['id'] = $id;
            $user['email']=$email;
            $user['name'] = $name;
            $user['school'] = $school;
            return $user;
        }


        /*
            The Update Operation
            The function will update an existing user
            from the database
        */
        public function updateUser($email, $name, $school, $id){
            $stmt = $this->con->prepare("UPDATE `users` SET `email` = ?, `name` = ?, `school` = ? WHERE `id`= ?");
            if($stmt->execute(array($email, $name, $school, $id))){
                return true;
              }else{
            return false;
          }
        }

        /*
            The Update Operation
            This function will update the password for a specified user
        */
        public function updatePassword($currentpassword, $newpassword, $email){
            $hashed_password = $this->getUsersPasswordByEmail($email);

            if(password_verify($currentpassword, $hashed_password)){

                $hash_password = password_hash($newpassword, PASSWORD_DEFAULT);
                $stmt = $this->con->prepare("UPDATE `users` SET `password` = ? WHERE `email` = ?");
                if($stmt->execute(array($hash_password, $email))){
                    return PASSWORD_CHANGED;
                  }else{

                    return PASSWORD_NOT_CHANGED;
                  }

            }else{
                return PASSWORD_DO_NOT_MATCH;
            }
        }

        /*
            The Delete Operation
            This function will delete the user from database
        */
        public function deleteUser($id){
            $stmt = $this->con->prepare("DELETE FROM `users` WHERE `id` = ?");
            if($stmt->execute(array($id))){
                $a= true;
            } else{
                $a= false;
            }

              return $a;
        }

        /*
            The Read Operation
            The function is checking if the user exist in the database or not
        */
        public function isEmailExist($email){
            $stmt = $this->con->prepare("SELECT * FROM `users` WHERE `email` = ?");

            $stmt->execute(array($email));
            $z=$stmt->fetch(PDO::FETCH_ASSOC);
            if($email===$z['email']){
              return false;
            }else{
              return true;
            }
        }
    }
