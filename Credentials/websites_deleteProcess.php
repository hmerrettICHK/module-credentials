<?php

/*
  Gibbon, Flexible & Open School System
  Copyright (C) 2010, Ross Parker

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

use Gibbon\Module\Credentials\CredentialsWebsiteGateway;
use Gibbon\Module\Credentials\CredentialsCredentialGateway;

include '../../gibbon.php';

$credentialsWebsiteID = $_GET['credentialsWebsiteID'] ?? '';

$URL = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address'])."/websites_delete.php&credentialsWebsiteID=".$credentialsWebsiteID;
$URLDelete = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/'.getModuleName($_POST['address'])."/websites.php";

if (isActionAccessible($guid, $connection2, '/modules/Credentials/websites_delete.php') == false) {
    //Fail 0
    $URL .= '&return=error0';
    header("Location: {$URL}");
} else {
    //Proceed!
    //Check if credentialsWebsiteID specified
    if ($credentialsWebsiteID == '') {
        echo __m('Fatal error loading this page!');
    } else {
        $data = array('credentialsWebsiteID' => $credentialsWebsiteID);
        $credentialsCredentialGateway = $container->get(CredentialsCredentialGateway::class);
        $credentialsCredentialGateway->deleteWhere($data);
        $credentials = $credentialsCredentialGateway->selectBy($data)->fetch();

        if (empty($credentials)) {
            //Write to database
            $credentialsWebsiteGateway = $container->get(CredentialsWebsiteGateway::class);
            $website = $credentialsWebsiteGateway->getById($credentialsWebsiteID);

            if ($website['logo'] != '') {
                $fileLogo = $_SESSION[$guid]['absolutePath'].'/'.$website['logo'];
                if (file_exists($fileLogo) and is_file($fileLogo)) {
                    unlink($fileLogo);
                }
            }
            $credentialsWebsiteGateway->delete($credentialsWebsiteID);

            //Success 0
            $URLDelete = $URLDelete.'&return=success0';
            header("Location: {$URLDelete}");
        }
    }
}
