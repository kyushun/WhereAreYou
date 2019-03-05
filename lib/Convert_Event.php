<?php
define('USER_DATA_PATH', realpath('../').'\data\users.json');
define('BLOCKUSER_DATA_PATH', realpath('../').'\data\blockusers.json');

class Calendar
{
    private $id;
    private $name;
    private $events;

    public function __construct($resourceId = null, $events = null)
    {
        if ($resourceId != null)
            $this->name = DomainUsers::getName($resourceId);
        $this->events = array();
        if ($events != null) {
            $this->id = $events->getSummary();
            foreach ($events->getItems() as $event) {
                $this->events[] = new Event($event);
            }
        }
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function toHash() {
        $hash = [
            'summary' => $this->id,
            'name' => $this->name,
            'events' => array()
        ];
        foreach ($this->events as $e) {
            $hash['events'][] = $e->toHash();
        }
        return $hash;
    }

    public function currentEvents() {
        $_events = array();
        foreach ($this->events as $e) {
            if ($e->isCurrent() === true) {
                $_events[] = $e;
            }
        }
        return $_events;
    }

    public function followEvents() {
        $_events = array();
        foreach ($this->events as $e) {
            if ($e->isLater() === true) {
                $_events[] = $e;
            }
        }
        return $_events;
    }
}

class Event
{
    private $id;
    private $summary;
    private $startTime;
    private $endTime;
    private $allDay;

    public function __construct($event)
    {
        $this->id = $event->getId();
        $this->summary = !empty($event->getSummary()) ? $event->getSummary() : '(タイトルなし)';
        $this->startTime = $event->start->getDateTime() !== null ? $event->start->getDateTime() : self::strToDate($event->start->getDate());
        $this->endTime = $event->end->getDateTime() !== null ? $event->end->getDateTime() : self::strToDate($event->end->getDate());
        $this->allDay = $event->start->getDateTime() === null ? true : false;
    }

    public function getId() { return $this->id; }
    public function getSummary() { return $this->summary; }
    public function getStartTime() { return $this->startTime; }
    public function getStartTimeAsTime() { return strtotime($this->startTime); }
    public function getEndTime() { return $this->endTime; }
    public function getEndTimeAsTime() { return strtotime($this->endTime); }
    public function getAllDay() { return $this->allDay; }

    public function isCurrent() {
        if ($this->allDay === true) return true;

        $now = time();
        $start = $this->getStartTimeAsTime();
        $end = $this->getEndTimeAsTime();

        if ($start <= $now && $now < $end) return true;
        return false;
    }

    public function isLater() {
        if ($this->allDay === true) return false;

        $now = time();
        $start = $this->getStartTimeAsTime();
        if ($now < $start) {
            return true;
        }
        return false;
    }

    public function toHash() {
        return [
            'id' => $this->getId(),
            'summary' => $this->getSummary(),
            'start' => $this->getStartTime(),
            'end' => $this->getEndTime(),
            'allDay' => $this->getAllDay()
        ];
    }

    public static function strToDate($time) {
        return date('Y-m-d\TH:i:s+09:00', strtotime('00:00:00', strtotime($time)));
    }
}

class Convert_Event
{
    public static function toHash($resourceId, $event) {
        return [
            'id' => $event->getId(),
            'summary' => !empty($event->getSummary()) ? $event->getSummary() : '(タイトルなし)',
            'start' => $event->start->getDateTime() !== null ? $event->start->getDateTime() : self::strToDate($event->start->getDate()),
            'end' => $event->end->getDateTime() !== null ? $event->end->getDateTime() : self::strToDate($event->end->getDate()),
            'allDay' => $event->start->getDateTime() === null ? true : false,
            'manager' => Convert_Email::getManager($resourceId, $event),
            'attendees' => self::getAttendeesList($resourceId, $event->attendees)
        ];
    }

    public static function strToDate($time) {
        return date('Y-m-d\TH:i:s+09:00', strtotime('00:00:00', strtotime($time)));
    }

    public static function toHashByList($resourceId, $events) {
        $res = [];
        $res['summary'] = $events->getSummary();
        $res['name'] = DomainUsers::getName($resourceId);
        $res['events'] = array();
        foreach ($events->getItems() as $event) {
            $res['events'][] = self::toHash($resourceId, $event);
        }
        return $res;
    }

    private static function hasCurrentEvent($event) {
        if ($event->start->getDate() != null) return false;
    }

    private static function getAttendeesList($resourceId, $attendees) {
        $res = [];
        foreach($attendees as $attender) {
            if ($resourceId != $attender->getEmail()) {
                $res[] = Convert_Email::getName($attender->getEmail(), $attender->getDisplayName());
            }
        }

        return $res;
    }

    public static function correntEvents($event) {
        $events = array();
        
    }
}

class Convert_Email
{
    public static function getManager($resourceId, $event) {
        try {
            
            if ($resourceId != $event->organizer->getEmail()) {

                return DomainUsers::getName($event->organizer->getEmail(), $event->organizer->getDisplayName());
    
            } else {
    
                return DomainUsers::getName($event->creator->getEmail(), $event->creator->getDisplayName());
    
            }

        } catch (Exception $ex) {
            return null;
        }
    }

    public static function getName($email, $name = null) {
        $registered = DomainUsers::getName($email);

        if ($registered != null) {
            return $registered;
        } else {
            if ($name != null) {
                return $name;
            } else {
                return $email;
            }
        }
    }
}

class DomainUsers
{
    public static function getName($email, $name = null) {
        $users = self::LoadDomainUsers();
        $registered = null;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $registered = $user['name'];
                break;
            }
        }

        if ($registered != null) {
            return $registered;
        } else {
            if ($name != null) {
                return $name;
            } else {
                return $email;
            }
        }
    }

    private static function LoadFromFile() {
        $users = file_get_contents(USER_DATA_PATH);
        $users = mb_convert_encoding($users, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $users = json_decode($users,true);
        return $users;
    }

    public static function LoadDomainUsers() {
        $users = self::LoadFromFile();

        for ($i = 0; $i < count($users); $i++) {
            if (isset($users[$i]['ignoreAllSearch']) && $users[$i]['ignoreAllSearch'] == true) {
                array_splice($users, $i, 1);
            }
        }

        return $users;
    }

    public static function Save($newUsers) {
        $users = [];
        $currentUsers = self::LoadFromFile();

        foreach ($newUsers as $nu) {
            $_u = $nu;
            foreach ($currentUsers as $cu) {
                if ($nu['name'] === $cu['name']) {
                    if (array_key_exists('phone', $cu)) {
                        $_u['phone'] = $cu['phone'];
                    }
                    break;
                }
            }
            $users[] = $_u;
        }

        file_put_contents(USER_DATA_PATH, json_encode($users, JSON_UNESCAPED_UNICODE));
    }

    public static function searchUsers($query, $limit = null) {
        if ($query === '') return [];
        $__users = [];

        $users = self::LoadDomainUsers();
        $count = 0;
        foreach ($users as $u) {
            // Ignore User
            if (isset($u['ignoreAllSearch']) && $u['ignoreAllSearch'] == true) continue;
            // Limit Break
            if ($limit !== null && $count >= $limit) break; 

            // Searching From Names
            if (!isset($u['ignoreNameSearch']) || $u['ignoreNameSearch'] != true) {
                if (isset($u['name']) && $u['name'] !== '' && strpos($u['name'], $query) !== false) {
                    $__users[] = $u;
                    $count++;
                    continue;
                }
            }

            // Searching From Emails
            if (!isset($u['ignoreEmailSearch']) || $u['ignoreEmailSearch'] != true) {
                if (isset($u['email']) && $u['email'] !== '' && strpos($u['email'], $query) !== false) {
                    $__users[] = $u;
                    $count++;
                    continue;
                }
            }
        }

        return $__users;
    }
}