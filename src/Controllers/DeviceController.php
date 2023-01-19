<?php
    namespace Src\Controllers;

    use Src\TableGateways\DeviceGateway;

    class DeviceController
    {
        private $db;
        private $requestMethod;
        private $device_id;
        private $location_id;
        private $deviceGateway;

        public function __construct($db, $requestMethod, $device_id, $location_id)
        {
            $this->db = $db;
            $this->requestMethod = $requestMethod;
            $this->device_id = $device_id;
            $this->location_id = $location_id;
            $this->deviceGateway = new DeviceGateway($db);
        }

        public function processRequest()
        {
            switch ($this->requestMethod)
            {
                case 'GET':
                    if ($this->device_id)
                    {
                        $response = $this->getDevice($this->device_id);
                    } elseif ($this->location_id)
                    {
                        $response = $this->getLocationDevice($this->location_id);
                    } else
                    {
                        $response = $this->getAllDevices();
                    }
                    break;
                case 'POST':
                    $response = $this->createDeviceFromRequest();
                    break;
                case 'PUT':
                    $response = $this->updateDeviceFromRequest($this->device_id);
                    break;
                case 'DELETE':
                    $response = $this->deleteDevice($this->device_id);
                    break;
                default:
                    $response = $this->notFoundResponse();
                    break;
            }
            header($response['status_code_header']);
            if ($response['body'])
            {
                echo $response['body'];
            }
        }

        private function getAllDevices()
        {
            $result = $this->deviceGateway->findAll();
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getDevice($id)
        {
            $result = $this->deviceGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] ='HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getLocationDevice($id)
        {
            $result = $this->deviceGateway->find_location($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] ='HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function createDeviceFromRequest()
        {
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateDevice($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->deviceGateway->insert($input);
            $response['status_code_header'] ='HTTP/1.1 201 Created';
            $response['body'] = null;
            return $response;

        }

        private function updateDeviceFromRequest($id)
        {
            $result = $this->deviceGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateDevice($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->deviceGateway->update($id, $input);
            $response['status_code_header'] ='HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }

        private function deleteDevice($id)
        {
            $result = $this->deviceGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $this->deviceGateway->delete($id);
            $response['status_code_header'] ='HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }

        private function validateDevice(array $input)
        {
            if (
                ! isset($input['name']) &&
                ! isset($input['location_id']) &&
                ! isset($input['mac'])
            )
            {
                return false;
            } else
            {
                return true;
            }
        }

        private function unprocessableEntityResponse()
        {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = json_encode([
                'error' => 'Invalid input'
            ]);
            return $response;
        }

        private function notFoundResponse()
        {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = null;
            return $response;
        }
    }