<?php
require_once 'Convert_Event.php';
define('APP_NAME', 'my_app');
define('SECRET_PATH', dirname(__FILE__).'\OAuth\client_secret.json');
define('CREDENTIAL_PATH', dirname(__FILE__).'\OAuth\credentials.json');
define('SCOPES', implode(' ', array(
    \Google_Service_Calendar::CALENDAR_READONLY,
    \Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY)));
define('USER_DATA_PATH', dirname(__FILE__).'\data\users.json');

class GoogleApi
{
    private $client;
    private $service;

    public function __construct() {
        $this->client = $this->getClient(APP_NAME, SECRET_PATH, CREDENTIAL_PATH, SCOPES);
        $this->service = new \Google_Service_Calendar($this->client);
        /*
        try {
            $this->client = $this->getClient(APP_NAME, SECRET_PATH, CREDENTIAL_PATH, SCOPES);
            $this->service = new \Google_Service_Calendar($this->client);
            break;
        } catch(Exception $e) {
            if (json_decode($e->getMessage())->error->message !== 'Invalid Credentials') {
                echo $e.'<br /><br /><br />';
                echo json_decode($e->getMessage())->error->message;
                echo '<br />a';
            }
        }
        */
    }

    public function getCalendar($id) {
        $params = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $this->getUtc(time()),
            'timeMax' => $this->getUtcNextday(time())
        );
        
        try {
            $events = $this->service->events->listEvents($id, $params);
            return new Calendar($id, $events);
        } catch (\Google_Service_Exception $e) {
            $errObj = json_decode($e->getMessage());
            if ($errObj->error->code === 404) {
                $res = [
                    'error' => 'Not Found',
                    'code' => '404'
                ];
                return $res;
            }
            var_dump($e->getMessage());
            exit();
        }
    }

    public function getEventList($id) {
        $params = array(
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $this->getUtc(time()),
            'timeMax' => $this->getUtcNextday(time())
        );
        
        try {
            $events = $this->service->events->listEvents($id, $params);
            $c = new Calendar($id, $events);
            echo json_encode($c->toHash());
            //var_dump($c->currentEvents());
            //var_dump($c->followEvents());
        } catch (\Google_Service_Exception $e) {
            $errObj = json_decode($e->getMessage());
            if ($errObj->error->code === 404) {
                $res = [
                    'error' => 'Not Found',
                    'code' => '404'
                ];
                return $res;
            }
            var_dump($e->getMessage());
            exit();
        }
        return Convert_Event::toHashByList($id, $events);
    }

    public function makeEvent($calId, $register, $title, $time) {
        $event = new \Google_Service_Calendar_Event([
            'summary' => $title,
            'description' => \Config::get('app.name').'から登録されました。',
            'start' => [
                'dateTime' => $this->getUtc(time()),
                'timeZone' => 'Asia/Tokyo',
            ],
            'end' => [
                'dateTime' => $this->getUtc(time() + $time * 60),
                'timeZone' => 'Asia/Tokyo',
            ],
            'attendees' => [
                [
                    'email' => $calId,
                    'self' => true,
                    'responseStatus' => 'accepted'
                ],
                [
                    'email' => $register,
                    'organizer' => true,
                    'responseStatus' => 'accepted'
                ],
            ],
            'extendedProperties' => [
                'shared' => [
                    'eventType' => 'none'
                ]
            ],
            'guestsCanModify' => true
        ]);
        $makedEvent = $this->service->events->insert($calId, $event);
        return 'success';
    }

    public function extendEvent($calId, $eventId, $extendTime) {
        try {
            $event = $this->service->events->get($calId, $eventId);
        
            $baseTime = strtotime($event->end->getDateTime());
            $extendedTime = $baseTime + $extendTime * 60;
            $event->end->setDateTime($this->getUtc($extendedTime));

            $newDescription = $event->getDescription();
            $newDescription = $newDescription."\r\n".date('m/d H:i', time()).' - ';
            $newDescription = $newDescription.\Config::get('app.name').'により延長されました';
            $newDescription = $newDescription.' (+'.$extendTime.'分)';
            $event->setDescription($newDescription);

            $extendedEvent = $this->service->events->update($calId, $eventId, $event);
        } catch (\Google_Service_Exception $e) {
            return [
                'error' => $e->getErrors()[0]['message']
            ];
        }
        return [
            'success' => $this->getUtc($extendedTime)
        ];
    }

    public function finishEvent($calId, $eventId) {
        try {
            $event = $this->service->events->get($calId, $eventId);
            $event->end->setDateTime($this->getUtc(time()));

            $newDescription = $event->getDescription();
            $newDescription = $newDescription."\r\n".date('m/d H:i', time()).' - ';
            $newDescription = $newDescription.\Config::get('app.name').'により退室しました';
            $event->setDescription($newDescription);

            $updatedEvent = $this->service->events->update($calId, $eventId, $event);
        } catch (\Google_Service_Exception $e) {
            return [
                'error' => $e->getErrors()[0]['message']
            ];
        }
        return [
            'success' => $this->getUtc(time())
        ];
    }

    /**
     * 指定した時間のUTC時間（RFC3339）を取得
     * 時間は00:00:00固定
     */
    private function getUtc($date) {
        return date('Y-m-d\TH:i:s+09:00', $date);
    }
    private function getUtcWithZero($date) {
        return date('Y-m-d\TH:i:s+09:00', strtotime('00:00:00', $date));
    }
    private function getUtcNextday($date) {
        return date('Y-m-d\TH:i:s+09:00', strtotime('00:00:00 +1 day', $date));
    }

    public function updateDomainUsers() {
        $users = $this->getDomainUsersList();
        DomainUsers::Save($users);
    }

    public function getDomainUsersList() {
        //$directoryClient = $this->getClient($appname, $secret, $credentials, $scope);
        $directoryService = new \Google_Service_Directory($this->client);
        
        // Print the first 10 users in the domain.
        $optParams = array(
            'customer' => 'my_customer',
            'maxResults' => 500
        );
        $results = $directoryService->users->listUsers($optParams);
        $users = [];
        
        foreach ($results->getUsers() as $user) {
            $users[] = [
                'email' => $user->getPrimaryEmail(),
                'name' => $user->getName()->getFullName()
            ];
        }
        return $users;
    }

    /**
    * Returns an authorized API client.
    * @return Google_Client the authorized client object
    */
    private function getClient($appname, $secret, $credentialsPath, $scope) {
        //$selfUrl = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://').$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/api/event/list/a';
        $selfUrl = 'http://localhost:8000';
        $tmpClient = new \Google_Client();
        $tmpClient->setApplicationName($appname);
        $tmpClient->setScopes($scope);
        $tmpClient->setAuthConfig($secret);
        $tmpClient->setRedirectUri($selfUrl);
        $tmpClient->setAccessType('offline');
        $tmpClient->setApprovalPrompt('force');

        //$credentialsPath = $credentials;
        // Load previously authorized credentials from a file.
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), 
            true);
        } else {
            // Request authorization from the user.
            $authUrl = $tmpClient->createAuthUrl();
            header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

            if (isset($_GET['code'])) {
                $authCode = $_GET['code'];
                // Exchange authorization code for an access token.
                $accessToken = $tmpClient->fetchAccessTokenWithAuthCode($authCode);
                header('Location: ' . filter_var($selfUrl, FILTER_SANITIZE_URL));
                if(!file_exists(dirname($credentialsPath))) {
                    mkdir(dirname($credentialsPath), 0700, true);
                }

                file_put_contents($credentialsPath, json_encode($accessToken));
            } else {
                exit('No code found');
            }
        }
        $tmpClient->setAccessToken($accessToken);

        // Refresh the token if it's expired.
        if ($tmpClient->isAccessTokenExpired()) {

            // save refresh token to some variable
            $refreshTokenSaved = $tmpClient->getRefreshToken();

            // update access token
            $tmpClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

            // pass access token to some variable
            $accessTokenUpdated = $tmpClient->getAccessToken();

            // append refresh token
            $accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

            //Set the new acces token
            $tmpClient->setAccessToken($accessTokenUpdated);

            // save to file
            file_put_contents($credentialsPath, json_encode($accessTokenUpdated));
        }

        return $tmpClient;
    }

    /**
    * Expands the home directory alias '~' to the full path.
    * @param string $path the path to expand.
    * @return string the expanded path.
    */
    /*
    private function expandHomeDirectory($path) {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }
    */
}