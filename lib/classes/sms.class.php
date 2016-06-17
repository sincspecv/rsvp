<?php
namespace TheFancyRobot\RSVP;
use \PDO;

class SMS extends DatabaseConnection {
  private $eventExists;
  private $eventCode;

  public $step;
  public $result;

  /**
   * Check if registration process has already started
   * @param  string $phoneNumber Phone number
   * @return boolean              
   */
  public function checkForSession ($phoneNumber) {
    $this->preparedQuery("SELECT * FROM sms WHERE phone_number = :phone_number");
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
    $this->result = $this->getSingleRow();

    if (!empty($this->result)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * Check if event code is valid
   * @param  string $eventCode Event code
   * @return boolean            
   */
  public function checkEventCode($eventCode) {
    $this->eventExists = $this->checkForTable($eventCode);

    if ($this->eventExists == TRUE) {
      return TRUE;
    } else {
      return FALSE;
    }
  }

  /**
   * Start SMS session by writing phone number to database
   * @param  string $phoneNumber Phone number
   */
  public function createSMSSession($phoneNumber) {
     //Add session to sms table
    $this->preparedQuery("INSERT INTO sms (phone_number, step) VALUES (:phone_number, :step)");
    $this->bind(':phone_number', $phoneNumber);
    $this->bind(':step', 1);
    $this->execute();    
  }  

  /**
   * Get step in registration process
   * @param  string $phoneNumber Phone number
   * @return string              
   */
  public function getStep($phoneNumber) {
    $this->preparedQuery("SELECT * FROM sms WHERE phone_number = :phone_number");
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
    $this->step = $this->getSingleRow();

    return $this->step['step'];
  } 

  /**
   * Add event code to database
   * @param  string $phoneNumber Phone number
   * @param  string $eventCode   Event code
   */
  public function processStepOne($phoneNumber, $eventCode) {
    $this->step = 2;
    $this->preparedQuery("UPDATE sms SET event_code = :event_code, step = :step WHERE phone_number = :phone_number");
    $this->bind(':event_code', $eventCode);
    $this->bind(':step', $this->step);
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
  }

  /**
   * Add name to database
   * @param  string $phoneNumber Phone number
   * @param  string $name        Name of registrant
   */
  public function processStepTwo($phoneNumber, $name) {
    $this->step = 3;
    $this->preparedQuery("UPDATE sms SET guest_name = :guest_name, step = :step WHERE phone_number = :phone_number");
    $this->bind(':guest_name', $name);
    $this->bind(':step', $this->step);
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
  }

  /**
   * Add number of additional guests to databse
   * @param  string $phoneNumber Phone number
   * @param  string $addGuests   Number of addtional guests
   */
    public function processStepThree($phoneNumber, $addGuests) {
    $this->step = 4;
    $this->preparedQuery("UPDATE sms SET add_guest = :add_guest, step = :step WHERE phone_number = :phone_number");
    $this->bind(':add_guest', $addGuests);
    $this->bind(':step', $this->step);
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
  }

  /**
   * Add guest to guest list
   * @param string $phoneNumber Phone number
   */
  public function addToGuestList($phoneNumber) {
    $this->preparedQuery("SELECT * FROM sms WHERE phone_number = :phone_number");
    $this->bind(':phone_number', $phoneNumber);
    $this->execute();
    $this->result = $this->getSingleRow();

    $this->eventCode = $this->result['event_code'];
    
    $this->preparedQuery("INSERT INTO $this->eventCode (guest_name, add_guest) VALUES (:guest_name, :add_guest)");
    $this->bind(':guest_name', $this->result['guest_name']);
    $this->bind(':add_guest', $this->result['add_guest']);
    $this->execute(); 
  }
}