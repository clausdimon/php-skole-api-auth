<?php
    namespace Src\TableGateways;

    class AlarmGateway
    {
        private $db = null;

        public function __construct($db)
        {
            $this->db = $db;
        }

        public function findAll()
        {
            $sql = "SELECT "
                . "alarm.id AS \"id\", "
                . "alarm.date_time AS \"date_time\", "
                . "alarm.isOn AS \"isOn\", "
                . "alarm.device_id AS \"device_id\", "
                . "device.name AS \"device_name\", "
                . "alarm.value AS \"value\", "
                . "alarm.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\" "
                . "FROM (( alarm "
                . "INNER JOIN device ON alarm.device_id = device.id) "
                . "INNER JOIN measuringUnit ON alarm.measuringUnit_id = measuringUnit.id) ";
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function find_id($id)
        {
            $sql = "SELECT "
                . "alarm.id AS \"id\", "
                . "alarm.date_time AS \"date_time\", "
                . "alarm.isOn AS \"isOn\", "
                . "alarm.device_id AS \"device_id\", "
                . "device.name AS \"device_name\", "
                . "alarm.value AS \"value\", "
                . "alarm.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\" "
                . "FROM (( alarm "
                . "INNER JOIN device ON alarm.device_id = device.id) "
                . "INNER JOIN measuringUnit ON alarm.measuringUnit_id = measuringUnit.id) "
                . "WHERE id = ?";
            $id = htmlspecialchars(strip_tags($id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_assoc();
            }catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }
        public function find_measurement($measuringUnit_id)
        {
            $sql = "SELECT "
                . "alarm.id AS \"id\", "
                . "alarm.date_time AS \"date_time\", "
                . "alarm.isOn AS \"isOn\", "
                . "alarm.device_id AS \"device_id\", "
                . "device.name AS \"device_name\", "
                . "alarm.value AS \"value\", "
                . "alarm.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\" "
                . "FROM (( alarm "
                . "INNER JOIN device ON alarm.device_id = device.id) "
                . "INNER JOIN measuringUnit ON alarm.measuringUnit_id = measuringUnit.id) "
                . "WHERE measuringUnit_id = ?";
            $measuringUnit_id = htmlspecialchars(strip_tags($measuringUnit_id));

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $measuringUnit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            }catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }
        public function insert(Array $input)
        {
            $statement = "INSERT INTO alarm (`date_time`, `isOn`, `device_id`, `value`, `measuringUnit_id`) VALUES(?,?,?,?,?)";
            $date_time = htmlspecialchars(strip_tags($input['date_time']));
            $isOn = htmlspecialchars(strip_tags($input['isOn']));
            $device_id = htmlspecialchars(strip_tags($input['device_id']));
            $value = htmlspecialchars(strip_tags($input['value']));
            $measuringUnit_id = htmlspecialchars(strip_tags($input['measuringUnit_id']));

            try {
                $stmt = $this->db->prepare($statement);
                $stmt->bind_param('siidi', $date_time, $isOn, $device_id, $value, $measuringUnit_id);
                $stmt->execute();

                return true;
            }catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }
        public function update($id, Array $input)
        {
            $sql = "UPDATE alarm SET date_time = ?, isOn = ?, device_id = ?, value = ?, measuringUnit_id = ? WHERE id = ?";
            $date_time = htmlspecialchars(strip_tags($input['date_time']));
            $isOn = htmlspecialchars(strip_tags($input['isOn']));
            $device_id = htmlspecialchars(strip_tags($input['device_id']));
            $value = htmlspecialchars(strip_tags($input['value']));
            $measuringUnit_id = htmlspecialchars(strip_tags($input['measuringUnit_id']));
            $id = htmlspecialchars(strip_tags($id));

            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('siidii', $date_time, $isOn, $device_id, $value, $measuringUnit_id, $id);
                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }
        public function delete($id)
        {
            $sql = "DELETE FROM alarm WHERE id = ?";
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