<?php
    namespace Src\TableGateways;

    class MeasuringGateway
    {
        private $db = null;

        public function __construct($db)
        {
            $this->db = $db;
        }

        public function findAll()
        {
            $sql = "SELECT "
                . "measurement.id AS \"id\", "
                . "measurement.value AS \"value\", "
                . "measurement.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurement.timestamp AS \"timestamp\", "
                . "measurement.device_id AS \"device_id\", "
                . "device.name AS \"device_name\" "
                . "FROM (( measurement "
                . "INNER JOIN measuringUnit ON measurement.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurement.device_id = device.id) ";

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function find_id($id)
        {
            $sql = "SELECT "
                . "measurement.id AS \"id\", "
                . "measurement.value AS \"value\", "
                . "measurement.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurement.timestamp AS \"timestamp\", "
                . "measurement.device_id AS \"device_id\", "
                . "device.name AS \"device_name\" "
                . "FROM (( measurement "
                . "INNER JOIN measuringUnit ON measurement.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurement.device_id = device.id) "
                . "WHERE id = ?";
            $id = htmlspecialchars(strip_tags($id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function find_measurement($measuringUnit_id)
        {
            $sql = "SELECT "
                . "measurement.id AS \"id\", "
                . "measurement.value AS \"value\", "
                . "measurement.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurement.timestamp AS \"timestamp\", "
                . "measurement.device_id AS \"device_id\", "
                . "device.name AS \"device_name\" "
                . "FROM (( measurement "
                . "INNER JOIN measuringUnit ON measurement.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurement.device_id = device.id) "
                . "WHERE measuringUnit_id = ?";
            $measuringUnit_id = htmlspecialchars(strip_tags($measuringUnit_id));

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $measuringUnit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function insert(Array $input)
        {
            $sql = "INSERT INTO measurement (`value`, `measuringUnit_id`, `timestamp`, `device_id`) VALUES(?,?,?,?)";
            $value = htmlspecialchars(strip_tags($input['value']));
            $measuringUnit_id = htmlspecialchars(strip_tags($input['measuringUnit_id']));
            $timestamp = htmlspecialchars(strip_tags($input['timestamp']));
            $device_id = htmlspecialchars(strip_tags($input['device_id']));

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('disi', $value, $measuringUnit_id, $timestamp, $device_id);
                $stmt->execute();

                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function update($id, Array $input)
        {
            $sql = "UPDATE measurement SET value = ?, measuringUnit_id = ?, timestamp = ?, device_id = ? WHERE id = ?";
            $value = htmlspecialchars(strip_tags($input['value']));
            $measuringUnit_id = htmlspecialchars(strip_tags($input['measuringUnit_id']));
            $timestamp = htmlspecialchars(strip_tags($input['timestamp']));
            $device_id = htmlspecialchars(strip_tags($input['device_id']));
            $id = htmlspecialchars(strip_tags($id));

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('disii', $value, $measuringUnit_id, $timestamp, $device_id, $id);
                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function delete($id)
        {
            $sql = "DELETE FROM measurement WHERE id = ?";
            $id = htmlspecialchars(strip_tags($id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $id);

                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

    }
