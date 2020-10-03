<?php
declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;

/**
 * Email component
 */
class EmailComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    public function Send(array $fromEmail, array $toEmail, array $data, string $templateId) : bool
    {
        $payload = ['from' => $fromEmail, 'personalizations' => [['to' => $toEmail, 'dynamic_template_data' => $data]], 'template_id' => $templateId];
        $header = ['Content-Type: application/json', 'Accept: application/json', 'Authorization: Bearer '.env('SENDGRID_API_KEY', null)];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $execute = curl_exec($ch);
        $isSuccess = true;
        if (curl_error($ch)) $isSuccess = false;
        curl_close($ch);
        if ($execute) {
            $res = get_object_vars(json_decode($execute));
            if (isset($res['errors']) && !empty($res['errors'])) $isSuccess = false;
        }
        return $isSuccess;
    }
    

}
