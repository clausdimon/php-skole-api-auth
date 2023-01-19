<?php
    namespace Src\Controllers;

    use Src\TableGateways\MeasuringGateway;

    class MeasuringController
    {
        private $db;
        private $requestMethod;
        private $measurment_id;
        private $measuringUnit_id;
        private $measuringGateway;

        public function __construct($db, $requestMethod, $measurment_id, $measuringUnit_id)
        {
            $this->db = $db;
            $this->requestMethod = $requestMethod;
            $this->measurment_id = $measurment_id;
            $this->measuringUnit_id = $measuringUnit_id;
            $this->measuringGateway = new MeasuringGateway($db);
        }

        public function processRequest()
        {
            switch ($this->requestMethod)
            {
                case 'GET':
                    if ($this->measurment_id)
                    {
                        $response = $this->getMeasurment($this->measurment_id);
                    } elseif ($this->measuringUnit_id)
                    {
                        $response = $this->getTypeMeasurment($this->measuringUnit_id);
                    } else
                    {
                        $response = $this->getAllMeasurments();
                    }
                    break;
                case 'POST':
                    $response = $this->createMeasurmentFromRequest();
                    break;
                case 'PUT':
                    $response = $this->updateMeasurmentFromRequest($this->measurment_id);
                    break;
                case 'DELETE':
                    $response = $this->deleteMeasurment($this->measurment_id);
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

        private function getMeasurment($id)
        {
            $result = $this->measuringGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getTypeMeasurment($id)
        {
            $result = $this->measuringGateway->find_measurement($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);

            return $response;
        }

        private function getAllMeasurments()
        {
            $result = $this->measuringGateway->findAll();
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function createMeasurmentFromRequest()
        {
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateMeasurment($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->measuringGateway->insert($input);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
            return $response;
        }

        private function updateMeasurmentFromRequest($id)
        {
            $result = $this->measuringGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateMeasurment($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->measuringGateway->update($id, $input);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }

        private function deleteMeasurment($id)
        {
            $result = $this->measuringGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $this->measuringGateway->delete($id);
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

        private function validateMeasurment(array $input)
        {
            if (
                ! isset($input['value']) &&
                ! isset($input['measuringUnit_id']) &&
                ! isset($input['timestamp']) &&
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