<?php
    class DbConnect{

        public $con;

        function connect(){
            include_once dirname(__FILE__)  . '/Constants.php';

            $this->con = new PDO('mysql:host=localhost;dbname=test','root','root');

            return $this->con;
        }

    }
