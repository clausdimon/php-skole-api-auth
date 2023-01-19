<?php
    namespace Src\TableGateways;

    class MeasurementConfigGateway
    {
        private $db = null;

        public function __construct($db)
        {
            $this->db = $db;
        }

        public function findAll()
        {
            $sql = "SELECT "
                . "measurmentConfig.name AS \"measurmentConfig_name\", "
                . "measurmentConfig.min, "
                . "measurmentConfig.max, "
                . "measurmentConfig.measuring_frequency_normal, "
                . "measurmentConfig.measuring_frequency_warning, "
                . "measurmentConfig.start_time, "
                . "measurmentConfig.end_time, "
                . "measurmentConfig.enabled, "
                . "measurmentConfig.measuringUnit_id, "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurmentConfig.device_id, "
                . "device.name AS \"device_name\", "
                . "device.mac AS \"device_mac\" "
                . "FROM (( measurmentConfig "
                . "INNER JOIN measuringUnit ON measurmentConfig.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurmentConfig.device_id = device.id)";
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function find_measurmentConfig($id)
        {
            $sql = "SELECT "
                . "measurmentConfig.name AS \"measurmentConfig_name\", "
                . "measurmentConfig.min, "
                . "measurmentConfig.max, "
                . "measurmentConfig.measuring_frequency_normal, "
                . "measurmentConfig.measuring_frequency_warning, "
                . "measurmentConfig.start_time, "
                . "measurmentConfig.end_time, "
                . "measurmentConfig.enabled, "
                . "measurmentConfig.measuringUnit_id, "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurmentConfig.device_id, "
                . "device.name AS \"device_name\", "
                . "device.mac AS \"device_mac\" "
                . "FROM (( measurmentConfig "
                . "INNER JOIN measuringUnit ON measurmentConfig.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurmentConfig.device_id = device.id) WHERE id = ?";
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

        public function find_measuringType($id)
        {
            $sql = "SELECT "
                . "measurmentConfig.name AS \"measurmentConfig_name\", "
                . "measurmentConfig.min, "
                . "measurmentConfig.max, "
                . "measurmentConfig.measuring_frequency_normal, "
                . "measurmentConfig.measuring_frequency_warning, "
                . "measurmentConfig.start_time, "
                . "measurmentConfig.end_time, "
                . "measurmentConfig.enabled, "
                . "measurmentConfig.measuringUnit_id, "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurmentConfig.device_id, "
                . "device.name AS \"device_name\", "
                . "device.mac AS \"device_mac\" "
                . "FROM (( measurmentConfig "
                . "INNER JOIN measuringUnit ON measurmentConfig.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurmentConfig.device_id = device.id) WHERE measuringUnit_id = ?";
            $id = htmlspecialchars(strip_tags($id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function find_device($id)
        {
            $sql = "SELECT "
                . "measurmentConfig.name AS \"measurmentConfig_name\", "
                . "measurmentConfig.min, "
                . "measurmentConfig.max, "
                . "measurmentConfig.measuring_frequency_normal, "
                . "measurmentConfig.measuring_frequency_warning, "
                . "measurmentConfig.start_time, "
                . "measurmentConfig.end_time, "
                . "measurmentConfig.enabled, "
                . "measurmentConfig.measuringUnit_id, "
                . "measuringUnit.name AS \"measuringUnit_name\", "
                . "measurmentConfig.device_id, "
                . "device.name AS \"device_name\", "
                . "device.mac AS \"device_mac\" "
                . "FROM (( measurmentConfig "
                . "INNER JOIN measuringUnit ON measurmentConfig.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN device ON measurmentConfig.device_id = device.id) WHERE device_id = ?";
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

        public function insert( Array $input)
        {
            $sql = "INSERT INTO measurementConfig "
                . "(`name`, `min`, `max`, `measuring_frequency_normal`, "
                . " `measuring_frequency_warning`, `start_time`, `end_time`, "
                . " `enabled`, `measuringUnit_id`, `device_id`) "
                ."VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $name = htmlspecialchars(strip_tags($input['name']));
            $min = htmlspecialchars(strip_tags($input['min']));
            $max = htmlspecialchars(strip_tags($input['max']));
            $mfn = htmlspecialchars(strip_tags($input['measuring_frequency_normal']));
            $mfw = htmlspecialchars(strip_tags($input['measuring_frequency_warning']));
            $start_time = htmlspecialchars(strip_tags($input['start_time']));
            $end_time = htmlspecialchars(strip_tags($input['end_time']));
            $enabled = htmlspecialchars(strip_tags($input['enabled']));
            $mUid = htmlspecialchars(strip_tags($input['measuringUnit_id']));
            $device_id = htmlspecialchars(strip_tags($input['device_id']));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('sddiissiii', $name, $min, $max, $mfn, $mfw, $start_time, $end_time, $enabled, $mUid, $device_id);
                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function update($id, Array $input)
        {
            $sql = "UPDATE measurementConfig SET "
                . "name = ?, min = ?, max = ?, "
                . "measuring_frequency_normal = ?, "
                . "measuring_frequency_warning = ?, "
                . "start_time = ?, end_time = ?, "
                . "enabled = ?, measuringUnit_id = ?, "
                . "device_id = ? WHERE id = ?";
            $name = htmlspecialchars(strip_tags($input['name']));
            $min = htmlspecialchars(strip_tags($input['min']));
            $max = htmlspecialchars(strip_tags($input['max']));
            $mfn = htmlspecialchars(strip_tags($input['measuring_frequency_normal']));
            $mfw = htmlspecialchars(strip_tags($input['measuring_frequency_warning']));
            $start_time = htmlspecialchars(strip_tags($input['start_time']));
            $end_time = htmlspecialchars(strip_tags($input['end_time']));
            $enabled = htmlspecialchars(strip_tags($input['enabled']));
            $mUid = htmlspecialchars(strip_tags($input['measuringUnit_id']));
            $device_id = htmlspecialchars(strip_tags($input['device_id']));
            $id = htmlspecialchars(strip_tags($id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('sddiissiiii', $name, $min, $max, $mfn, $mfw, $start_time, $end_time, $enabled, $mUid, $device_id, $id);
                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function delete($id)
        {
            $sql = "DELETE FROM measurementConfig WHERE id = ?";
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
