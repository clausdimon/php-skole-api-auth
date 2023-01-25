<?php
    namespace Src\Controllers;

    use Src\TableGateways\MeasurementConfigGateway;

    class MeasurmentConfigController
    {
        private $db;
        private $requestMethod;
        private $measurementconfig_id;
        private $measuringUnit_id;
        private $device_id;
        private $mac;
        private $measurementconfigGateway;

        public function __construct($db, $requestMethod, $measurementconfig_id, $measuringUnit_id, $device_id, $mac)
        {
            $this->db = $db;
            $this->requestMethod = $requestMethod;
            $this->measurementconfig_id = $measurementconfig_id;
            $this->measuringUnit_id = $measuringUnit_id;
            $this->device_id = $device_id;
            $this->mac = $mac;
            $this->measurementconfigGateway = new MeasurementConfigGateway($db);
        }

        public function processRequest()
        {
            switch ($this->requestMethod)
            {
                case 'GET':
                    if ($this->measurementconfig_id)
                    {
                        $response = $this->getConfigWithId($this->measurementconfig_id);
                    } elseif ($this->measuringUnit_id)
                    {
                        $response = $this->getConfigForType($this->measuringUnit_id);
                    } elseif ( $this->device_id)
                    {
                        $response = $this->getDeviceConfig($this->device_id);
                    } elseif ($this->mac)
                    {
                     $response = $this->getConfigWithMac($this->mac);
                    } else
                    {
                        $response = $this->getAllConfig();
                    }
                    break;
                case 'POST':
                    $response = $this->createConfigFromRequest();
                    break;
                case 'PUT':
                    $response = $this->updateConfigFromRequest($this->measurementconfig_id);
                    break;
                case 'DELETE':
                    $response = $this->deleteConfig($this->measurementconfig_id);
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

        private function getAllConfig()
        {
            $result = $this->measurementconfigGateway->findAll();
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getConfigWithId($id)
        {
            $result = $this->measurementconfigGateway->find_measurementconfig($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getConfigForType($id)
        {
            $result = $this->measurementconfigGateway->find_measuringType($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getDeviceConfig($id)
        {
            $result = $this->measurementconfigGateway->find_device($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getConfigWithMac($mac)
        {
            $result = $this->measurementconfigGateway->find_config_by_mac($mac);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function createConfigFromRequest()
        {
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateConfig($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->measurementconfigGateway->insert($input);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
            return $response;
        }

        private function updateConfigFromRequest($id)
        {
            $result = $this->measurementconfigGateway->find_measurementconfig($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateConfig($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->measurementconfigGateway->update($id, $input);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }

        private function deleteConfig($id)
        {
            $result = $this->measurementconfigGateway->find_measurementconfig($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $this->measurementconfigGateway->delete($id);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }

        private function notFoundResponse()
        {
            $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
            $response['body'] = null;
            return $response;
        }

        private function validateConfig(array $input)
        {
            if (
                ! isset($input['name']) &&
                ! isset($input['min']) &&
                ! isset($input['max']) &&
                ! isset($input['measuring_frequency_normal']) &&
                ! isset($input['measuring_frequency_warning']) &&
                ! isset($input['start_time']) &&
                ! isset($input['end_time']) &&
                ! isset($input['enabled']) &&
                ! isset($input['measuringUnit_id']) &&
                ! isset($input['device_id'])
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

    }
