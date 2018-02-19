<?php

    namespace app\components;

    use Yii;

class Helper {

    public static $data = [];

    /**
     * Отправка письма по шаблону
     *
     * @param $template
     * @param $title
     * @param $user
     * @param array $params
     * @return bool
     */
    public static function sendMail($template, $title, $user, $params = [])
    {
        Yii::$app->params['user'] = $params['user'] = $user;
        $params['host'] = \yii\helpers\Url::home(true);

        $message = Yii::$app->mailer->compose($template, $params)
            ->setSubject( Yii::t('app', $title) )
            ->setFrom([Yii::$app->params['noreply'] => Yii::$app->params['from']])
            ->setTo($user->email);

        try {
            $res = $message->send();
        } catch (\Throwable $e) {
            $res = false;
        }
        return $res;
    }

    public static function plural($number, $titles)
    {
        $cases = [2, 0, 1, 1, 1, 2];
        return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[($number%10<5)?$number%10:5] ];
    }

    public static function checkCaptcha($recaptchaVal = false)
    {
        $captcha = $recaptchaVal ? $recaptchaVal : self::getRequestParam('g-recaptcha-response');
        if (empty($captcha))
            return false;
        if (false === ($res = self::getUrl('https://www.google.com/recaptcha/api/siteverify?secret='.rawurlencode(Yii::$app->params['recaptcha_secret']).
            '&response='.rawurlencode($captcha).'&remoteip='.rawurlencode(Yii::$app->request->userIP))))
            return false;
        if (false === ($res = json_decode($res, true)))
            return false;
        if (empty($res['success']))
            return false;
        if (empty($res['hostname']) || Yii::$app->request->hostName != $res['hostname'])
            return false;
        // с момента отгадывания капчи прошло не более 10 минут
        if (empty($res['challenge_ts']) || time() > strtotime($res['challenge_ts']) + 3600 + 600)
            return false;

        return true;
    }

    public static function clearSessionStorage($key)
    {
        if (!$key) {
            return;
        }

        $session = Yii::$app->session;
        if ($session->has($key)) {
            $session->remove($key);
        }
    }

    public static function getUrl($url, $data=[])
    {
        $url = str_replace(' ', '%20', $url);
        $c = curl_init();
        if ($c===false)
            return false;
        $https = (mb_substr($url,0,5) === 'https');
        curl_setopt_array($c, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => (int)(!$https) && (!isset($data['followlocation']) || $data['followlocation']),//https не поддерживает опцию follow location
            CURLOPT_SSL_VERIFYPEER => (isset($data['CURLOPT_SSL_VERIFYPEER'])? $data['CURLOPT_SSL_VERIFYPEER'] : 0),//проверка сертификатов, по умолчанию отключена
            CURLOPT_SSL_VERIFYHOST => (isset($data['CURLOPT_SSL_VERIFYHOST'])? $data['CURLOPT_SSL_VERIFYHOST'] : FALSE),//проверка сертификатов, по умолчанию отключена
        ]);

        if (isset($data['put'])) {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT');
        } elseif (isset($data['delete'])) {
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        if (isset($data['post']))	//POST запрос
        {
            if (!isset($data['put'])) // PUT поля отправляет аналогично POST
                curl_setopt($c,CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_POSTFIELDS, is_array($data['post']) ? http_build_query($data['post']) : $data['post']);
        }

        if (isset($data['proxy'])) { // PROXY
            curl_setopt($c, CURLOPT_PROXY, $data['proxy']['host']);
            if (isset($data['proxy']['auth']))
                curl_setopt($c, CURLOPT_PROXYUSERPWD, $data['proxy']['auth']);
        }

        if (isset($data['socs5']))
            curl_setopt($c, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);

        $timeout = isset($data['timeout']) ? $data['timeout'] : false;
        if (empty($data['file']) && $timeout !== false)
            curl_setopt($c, CURLOPT_TIMEOUT, $timeout);
        if (!empty($data['user']))
            curl_setopt($c, CURLOPT_USERPWD, $data['user'] . ':' . $data['pass']);
        if (!empty($data['file'])){
            if (false === ($hFile = fopen($data['file'], 'w')))
                return false;
            curl_setopt($c, CURLOPT_FILE, $hFile);
            curl_setopt($c, CURLOPT_HEADER, 0);
        }
        if (!empty($data['agent']))
            curl_setopt($c, CURLOPT_USERAGENT, $data['agent']);
        if (!empty($data['headers']))
            curl_setopt($c, CURLOPT_HTTPHEADER, $data['headers']);
        if (!empty($data['header']))
            curl_setopt($c, CURLOPT_HEADER, 1);
        if (!empty($data['referer']))
            curl_setopt($c, CURLOPT_REFERER, $data['referer']);
        if (!empty($data['cookie']))
            curl_setopt($c, CURLOPT_COOKIE, $data['cookie']);

        $content = curl_exec($c);
        $header = '';
        if (!empty($data['header'])) {
            $header_size = curl_getinfo($c, CURLINFO_HEADER_SIZE);
            $header = trim(substr($content, 0, $header_size));
            $content = substr($content, $header_size);
            if ($header) {
                $tmp = explode("\n", $header);
                $header = [];
                foreach($tmp as $line) {
                    if (false !== ($p = stripos($line, ':')))
                        $header[trim(substr($line, 0, $p))] = trim(substr($line, $p + 1));
                }
            }
        }

        $code = curl_getinfo($c, CURLINFO_HTTP_CODE);
        $errno = curl_errno($c);
        if ($errno)
            self::$data['curl_err'] = array(
                'errno' => $errno,
                'error' => curl_error($c),
                'info' => curl_getinfo($c)
            );
        curl_close($c);
        if ($errno == 28){ //CURLE_OPERATION_TIMEDOUT
            return false;
        }
        if (!empty($data['file'])){
            fclose($hFile);
            return true;
        }
        if (array_key_exists('rcode',$data))
            return [
                'code' => $code,
                'content' => $content,
                'header' => $header,
            ];
        if ($code >= 300)
            return false;
        return $content;
    }
}