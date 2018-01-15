<?php

namespace core\alarm;

use core\database\PortalDBController;
use core\database\RegistrationDBController;
use core\notification\NotificationController;
use core\rest\SuccessResponse;
use core\utils\ConfigReader;

class AlarmController
{

  /**
   * @var RegistrationDBController
   */
  private $registrationDBController;
  /**
   * @var PortalDBController
   */
  private $portalDBController;

  public function __construct()
  {
    $this->registrationDBController = new RegistrationDBController();
    $this->portalDBController = new PortalDBController();
  }

  public function alarmTriggered($chubId, $triggerName, array $snapshots)
  {
    $this->registrationDBController->checkChubId($chubId);

    $notificationId = $this->portalDBController->saveNotification($chubId, $triggerName);

    $this->convertSnapshotsToImage($notificationId, $chubId, $snapshots);

    NotificationController::sendNotification(
      'Alarm \'' . $triggerName . '\' was triggered in your house!',
      'Alarm triggered',
      $chubId
    );

    return new SuccessResponse(SuccessResponse::HTTP_CREATED, 'Done.');

  }

  private function convertSnapshotsToImage($notificationId, $chubId, array $snapshots)
  {
    $configReader = new ConfigReader('snapshots');
    $filePath = $configReader->requireConfig('imageSavePath');

    foreach ($snapshots as $index => $snapshot) {
      $pos = strpos($snapshot, ';');
      $type = explode('/', explode(':', substr($snapshot, 0, $pos))[1])[1];

      $name = $chubId . '-' . time() . '-' . $index . '.' . $type;
      $imageName = $filePath . $name;

      $ifp = fopen($imageName, 'wb');
      $data = explode(',', $snapshot);
      fwrite($ifp, base64_decode($data[1]));
      fclose($ifp);
      $this->portalDBController->saveSnapshot($notificationId, $name);
    }
  }

  public function alarmStatusUpdate($chubId, $status) {
    if($status == 'enable') {
      $status = 'armed';
      NotificationController::sendNotification('The alarm was turned on.', 'Alarm status change', $chubId);
    } else {
      $status = 'off';
      NotificationController::sendNotification('The alarm was turned off.', 'Alarm status change', $chubId);
    }
    $this->portalDBController->alarmStatusUpdate($chubId, $status);
    return new SuccessResponse(SuccessResponse::HTTP_OK, 'Status updated.');
  }

}