<?php
namespace app\components;

use yii\base\Component;

class SmsPilot extends Component
{
    public string $apiKey;
    public string $sender;
    public string  $apiUrl  = 'https://smspilot.ru/api.php';
    public bool    $enabled = false;

    public function send(string $phone, string $text): array {
        return $this->sendMany([$phone], $text);
    }

    public function sendMany(array $phones, string $text): array {
        if (!$this->enabled) {
            \Yii::info(['smsPilot'=>'disabled','phones'=>$phones,'text'=>$text], __METHOD__);
            return ['ok'=>true,'response'=>'disabled'];
        }
        if (!$this->apiKey) {
            \Yii::error('SmsPilot: apiKey is empty', __METHOD__);
            return ['ok'=>false,'response'=>null,'error'=>'Empty API key'];
        }

        $to = implode(',', array_filter(array_map('trim', $phones)));
        if ($to==='') return ['ok'=>false,'response'=>null,'error'=>'Empty recipients'];

        $query = http_build_query([
            'send'   => $text,
            'to'     => $to,
            'apikey' => $this->apiKey,
            'from'   => $this->sender,
            'format' => 'json',
        ]);
        $url = rtrim($this->apiUrl, '?') . '?' . $query;

        try {
            $json = @file_get_contents($url);
            if ($json === false) {
                $err = error_get_last()['message'] ?? 'connection error';
                \Yii::error(['smsPilot'=>'connection_error','error'=>$err], __METHOD__);
                return ['ok'=>false,'response'=>null,'error'=>$err];
            }
            $data = json_decode($json, true);
            if (isset($data['error'])) {
                \Yii::error(['smsPilot'=>'api_error','error'=>$data['error']], __METHOD__);
                return ['ok'=>false,'response'=>$data,'error'=>$data['error']['description_ru'] ?? 'API error'];
            }
            \Yii::info(['smsPilot'=>'ok','response'=>$data], __METHOD__);
            return ['ok'=>true,'response'=>$data];
        } catch (\Throwable $e) {
            \Yii::error(['smsPilot'=>'exception','error'=>$e->getMessage()], __METHOD__);
            return ['ok'=>false,'response'=>null,'error'=>$e->getMessage()];
        }
    }
}