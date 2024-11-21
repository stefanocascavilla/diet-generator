<?php

class UserModel {
    private int $id;
    private string $name;
    private string $surname;
    private string $email;
    private string $phone;

    function __construct(int $id, string $name, string $surname, string $email, string $phone) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function get_id () {
        return $this->id;
    }

    public function get_email () {
        return $this->email;
    }

    public function get_phone () {
        return $this->phone;
    }

    public function get_name () {
        return $this->name;
    }

    public function get_surname () {
        return $this->surname;
    }
}

?>