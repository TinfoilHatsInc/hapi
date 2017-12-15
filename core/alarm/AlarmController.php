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
    $chub = $this->registrationDBController->checkChubId($chubId);

    $notificationId = $this->portalDBController->saveNotification($chubId, $triggerName);

    $this->convertSnapshotsToImage($notificationId, $chubId, $snapshots);

    NotificationController::sendNotification(
      'Alarm \'' . $triggerName . '\' was triggered in your house!',
      'Alarm triggered',
      $chub['userid']
    );

    return new SuccessResponse(SuccessResponse::HTTP_CREATED, 'Done.');

  }

  private function convertSnapshotsToImage($notificationId, $chubId, array $snapshots)
  {

    $configReader = new ConfigReader('snapshots');
    $filePath = $configReader->requireConfig('imageSavePath');

    $imageFiles = [];

    foreach ($snapshots as $index => $snapshot) {
      $pos = strpos($snapshot, ';');
      $type = explode('/', explode(':', substr($snapshot, 0, $pos))[1])[1];

      $imageName = $filePath . $chubId . '-' . time() . '-' . $index . '.' . $type;

      $ifp = fopen($imageName, 'wb');
      $data = explode(',', $snapshot);
      fwrite($ifp, base64_decode($data[1]));
      fclose($ifp);
      $imageFiles[] = $imageName;
      $this->portalDBController->saveSnapshot($notificationId, $imageName);
    }

  }

}