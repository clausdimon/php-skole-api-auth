<?php
    namespace Src\TableGateways;

    use \DateTime;
    use \DateTimeZone;

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
                . "location.name AS \"location_name\", "
                . "greenhouse.name AS \"greenhouse_name\", "
                . "greenhouse.address AS \"greenhouse_address\", "
                . "alarm.value AS \"value\", "
                . "alarm.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\" "
                . "FROM (((( alarm "
                . "INNER JOIN device ON alarm.device_id = device.id) "
                . "INNER JOIN measuringUnit ON alarm.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN location ON device.location_id = location.id) "
                . "INNER JOIN greenhouse ON location.greenhouse_id = greenhouse.id)";
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
                . "location.name AS \"location_name\", "
                . "greenhouse.name AS \"greenhouse_name\", "
                . "greenhouse.address AS \"greenhouse_address\", "
                . "alarm.value AS \"value\", "
                . "alarm.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\" "
                . "FROM (((( alarm "
                . "INNER JOIN device ON alarm.device_id = device.id) "
                . "INNER JOIN measuringUnit ON alarm.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN location ON device.location_id = location.id) "
                . "INNER JOIN greenhouse ON location.greenhouse_id = greenhouse.id) "
                . "WHERE alarm.id = ?";
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
                . "location.name AS \"location_name\", "
                . "greenhouse.name AS \"greenhouse_name\", "
                . "greenhouse.address AS \"greenhouse_address\", "
                . "alarm.value AS \"value\", "
                . "alarm.measuringUnit_id AS \"measuringUnit_id\", "
                . "measuringUnit.name AS \"measuringUnit_name\" "
                . "FROM (((( alarm "
                . "INNER JOIN device ON alarm.device_id = device.id) "
                . "INNER JOIN measuringUnit ON alarm.measuringUnit_id = measuringUnit.id) "
                . "INNER JOIN location ON device.location_id = location.id) "
                . "INNER JOIN greenhouse ON location.greenhouse_id = greenhouse.id) "
                . "WHERE alarm.measuringUnit_id = ?";
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
            $date_time = $this->getDateAndTime();
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
            $sql = "UPDATE alarm SET date_time = ?, isOn = ?, device_id = ?, value = ?, measuringUnit_id = ? WHERE alarm.id = ?";
            $date_time = $this->getDateAndTime();
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
            $sql = "DELETE FROM alarm WHERE alarm.id = ?";
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

        private function getDateAndTime()
        {
            $tz = 'Europe/Copenhagen';
            $timestamp = time();
            $dt = new DateTime('now', new DateTimeZone($tz));
            $dt->setTimestamp($timestamp);
            return $dt->format('Y.m.d H:i:s');
        }


    }