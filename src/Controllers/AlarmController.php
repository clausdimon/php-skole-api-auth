<?php
    namespace Src\Controllers;

    use http\Env\Response;
    use Src\TableGateways\AlarmGateway;

    class AlarmController
    {
        private $db;
        private $requestMethod;
        private $alarm_id;
        private $measuringUnit_id;
        private  $alarmGateway;

        public function __construct($db, $requestMethod, $alarm_id, $measuringUnit_id)
        {
            $this->db = $db;
            $this->requestMethod = $requestMethod;
            $this->alarm_id = $alarm_id;
            $this->measuringUnit_id = $measuringUnit_id;
            $this->alarmGateway = new AlarmGateway($db);
        }
        public function processRequest()
        {
            switch ($this->requestMethod) {
                case 'GET':
                    if ($this->alarm_id)
                    {
                        $response = $this->getAlarm($this->alarm_id);
                    } elseif ($this->measuringUnit_id)
                    {
                        $response = $this->getTypeAlarm($this->measuringUnit_id);
                    } else {
                        $response = $this->getAllAlarms();
                    }
                    break;
                case 'POST':
                    $response = $this->createAlarmFromRequest();
                    break;
                case 'PUT':
                    $response = $this->updateAlarmFromRequest($this->alarm_id);
                    break;
                case 'DELETE':
                    $response = $this->deleteAlarm($this->alarm_id);
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

        private function getAllAlarms()
        {
            $result = $this->alarmGateway->findAll();
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return $response;
        }

        private function getAlarm($id)
        {
            $result = $this->alarmGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);
            return  $response;
        }

        private function getTypeAlarm($id)
        {
            $result = $this->alarmGateway->find_measurement($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($result);

            return $response;
        }

        private function createAlarmFromRequest()
        {
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateAlarm($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->alarmGateway->insert($input);
            $response['status_code_header'] = 'HTTP/1.1 201 Created';
            $response['body'] = null;
            return $response;
        }

        private function updateAlarmFromRequest($id)
        {
            $result = $this->alarmGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $input = (array) json_decode(file_get_contents('php://input'), true);
            if (! $this->validateAlarm($input))
            {
                return $this->unprocessableEntityResponse();
            }
            $this->alarmGateway->update($id, $input);
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = null;
            return $response;
        }

        private function deleteAlarm($id)
        {
            $result = $this->alarmGateway->find_id($id);
            if (! $result)
            {
                return $this->notFoundResponse();
            }
            $this->alarmGateway->delete($id);
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

        private function validateAlarm(array $input)
        {
            if (
                ! isset($input['isOn']) &&
                ! isset($input['device_id']) &&
                ! isset($input['value']) &&
                ! isset($input['measuringUnit_id'])
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