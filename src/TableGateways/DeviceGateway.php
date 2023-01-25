<?php
    namespace Src\TableGateways;

    class DeviceGateway
    {
        private $db = null;

        public function __construct($db)
        {
            $this->db = $db;
        }

        public function findAll()
        {
            $sql = "SELECT "
                . "device.id, "
                . "device.name AS \"device_name\", "
                . "location.name AS \"location_name\", "
                . "greenhouse.name AS \"greenhouse_name\", "
                . "greenhouse.address AS \"greenhouse_address\", "
                . "device.mac "
                . "FROM (( device "
                . "INNER JOIN location ON device.location_id = location.id) "
                . "INNER JOIN greenhouse ON location.greenhouse_id = greenhouse.id)";
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
                . "device.id, "
                . "device.name AS \"device_name\", "
                . "location.name AS \"location_name\", "
                . "greenhouse.name AS \"greenhouse_name\", "
                . "greenhouse.address AS \"greenhouse_address\", "
                . "device.mac "
                . "FROM (( device "
                . "INNER JOIN location ON device.location_id = location.id) "
                . "INNER JOIN greenhouse ON location.greenhouse_id = greenhouse.id) "
                . "WHERE device.id = ?";
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

        public function find_location($location_id)
        {
            $sql = "SELECT "
                . "device.id, "
                . "device.name AS \"device_name\", "
                . "location.name AS \"location_name\", "
                . "greenhouse.name AS \"greenhouse_name\", "
                . "greenhouse.address AS \"greenhouse_address\", "
                . "device.mac "
                . "FROM (( device "
                . "INNER JOIN location ON device.location_id = location.id) "
                . "INNER JOIN greenhouse ON location.greenhouse_id = greenhouse.id) "
                . "WHERE device.location_id = ?";
            $location_id = htmlspecialchars(strip_tags($location_id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('i', $location_id);
                $stmt->execute();
                $result = $stmt->get_result();
                return $result->fetch_all(MYSQLI_ASSOC);
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function insert(Array $input)
        {
            $sql = "INSERT INTO device (`name`, `location_id`, `mac`) VALUES (?,?,?)";
            $name = htmlspecialchars(strip_tags($input['name']));
            $location_id = htmlspecialchars(strip_tags($input['location_id']));
            $mac = htmlspecialchars(strip_tags($input['mac']));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('sis', $name, $location_id, $mac);
                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function update($id, Array $input)
        {
            $sql = "UPDATE device SET name = ?, location_id = ?, mac = ? WHERE device.id = ?";
            $name = htmlspecialchars(strip_tags($input['name']));
            $location_id = htmlspecialchars(strip_tags($input['location_id']));
            $mac = htmlspecialchars(strip_tags($input['mac']));
            $id = htmlspecialchars(strip_tags($id));
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->bind_param('sisi', $name, $location_id, $mac, $id);
                $stmt->execute();
                return true;
            } catch (\mysqli_sql_exception $e) {
                exit($e->getMessage());
            }
        }

        public function delete($id)
        {
            $sql = "DELETE FROM device WHERE device.id = ?";
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