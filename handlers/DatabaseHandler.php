<?php

class DatabaseHandlerClass {
    private $db_connection;

    function __construct (string $host, string $username, string $password, string $database) {
        $this->db_connection = new mysqli($host, $username, $password, $database, 20073);
    }

    public function close_connection () {
        $this->db_connection->close();
    }

    public function execute_query (string $query, array $query_params) {
        $stmt = $this->db_connection->prepare($query);
        foreach($query_params as $key => $value) {
            $stmt->bind_param($value, $key);
        }
        $stmt->execute();

        return $stmt->get_result();
    }

    public function insert_new_user (string $query, array $user_info) {
        $stmt = $this->db_connection->prepare($query);
        $stmt->bind_param(
            "ssss",
            $user_info['name'],
            $user_info['surname'],
            $user_info['email'],
            $user_info['phone'],
        );
        $stmt->execute();
    }

    public function update_user_password (string $query, string $new_password, string $email) {
        $stmt= $this->db_connection->prepare($query);
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();
    }

    public function insert_dog_info (string $query, string $dog_id, int $user_id, array $dog_info) {
        $stmt = $this->db_connection->prepare($query);
        $stmt->bind_param(
            "sssssiisi",
            $dog_id,
            $dog_info['name_owner'],
            $dog_info['surname_owner'],
            $dog_info['dog_name'],
            $dog_info['dog_race'],
            $dog_info['weight_kg'],
            $dog_info['age'],
            $dog_info['status'],
            $user_id
        );
        $stmt->execute();
    }

    public function update_dog_info (string $query, string $dog_id, array $dog_info) {
        $stmt = $this->db_connection->prepare($query);
        $stmt->bind_param(
            "ssssiiss",
            $dog_info['name_owner'],
            $dog_info['surname_owner'],
            $dog_info['dog_name'],
            $dog_info['dog_race'],
            $dog_info['weight_kg'],
            $dog_info['age'],
            $dog_info['status'],
            $dog_id
        );
        $stmt->execute();
    }

    public function insert_update_dog_body_info (string $query, string $dog_id, array $dog_body_info, string $dog_user_think) {
        $stmt = $this->db_connection->prepare($query);
        $stmt->bind_param(
            "dddss",
            $dog_body_info['height_cm'],
            $dog_body_info['circ_chest_cm'],
            $dog_body_info['circ_abs_cm'],
            $dog_user_think,
            $dog_id
        );
        $stmt->execute();
    }

    public function insert_update_dog_sick_info (string $query, string $dog_id, array $dog_sick_info) {
        $stmt = $this->db_connection->prepare($query);
        $stmt->bind_param(
            "ss",
            $dog_sick_info['dog_sicks'],
            $dog_id
        );
        $stmt->execute();
    }

    public function insert_update_dog_tab_info (string $query, string $dog_id, array $dog_tab_info) {
        $stmt = $this->db_connection->prepare($query);
        $stmt->bind_param(
            "dds",
            $dog_tab_info['daily_kcal'],
            $dog_tab_info['omega_3'],
            $dog_id
        );
        $stmt->execute();
    }
}

?>