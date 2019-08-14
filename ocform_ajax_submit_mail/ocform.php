<?php
class ControllerAjaxFeedback extends Controller
{
    public function ajaxSend()
    {
        //проверяем была ли отправлена форма. Если нет, завершаем работу контроллера
        if ($this->request->server['REQUEST_METHOD'] !== 'POST'){
            return false;
        }
        //извлекаем имя домена из настроек CMS.
        $parse = parse_url($this->config->get('config_url'));
        $domain = $parse['host'];
        //поскольку ответ отправляем в формате JSON, необходимо установить заголовок
        $this->response->addHeader('Content-Type: application/json');
        //здесь валидация того, что форма отправлена именно через AJAX.
        //просто как дополнительная защита от спама
        if (!isset($this->request->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->request->server['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'
        ) {
            $output = json_encode([
                'type'=>'error',
                'text' => 'Sorry Request must be Ajax POST',
            ]);

            $this->response->setOutput($output);
            return false;
        }

        $user_name = $this->request->post['name'];
        $phone_number = $this->request->post['phone'];
        $mail_user = $this->request->post['email'];
        $message = $this->request->post['text'];

        //проверяем длину имени пользователя
        if (strlen($user_name) < 4) {

            $output = json_encode([
                'type'=>'error',
                'text' => 'Слишком короткое имя',
            ]);

            $this->response->setOutput($output);
            return false;
        }
        if (!filter_var($phone_number, FILTER_SANITIZE_NUMBER_FLOAT)) {
            $output = json_encode([
                'type'=>'error',
                'text' => 'Некорректный телефон',
            ]);
            $this->response->setOutput($output);
            return false;
        }
        // устанавливаем дополнительный параметр для корректного return-path
        $mail = new Mail(['parameter' => '-finfo@' . $domain]);
        //извлекаем различные данные из настроек магазина и заполняем в экземпляр mail.
        $mail->protocol = $this->config->get('config_mail_protocol');

        //тут смотрим есть ли в конфигах дополнительные параметры.
        //вероятно флаг –f или иной тюнинг был указан в админке, тогда перезаписываем.
        if (!empty($this->config->get('config_mail_parameter'))) {
            $mail->parameter = $this->config->get('config_mail_parameter');
        }

        $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
        $mail->smtp_username = $this->config->get('config_mail_smtp_username');
        $mail->smtp_password = $this->config->get('config_mail_smtp_password');
        $mail->smtp_port = $this->config->get('config_mail_smtp_port');
        $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
        $mail->setTo($this->config->get('config_email'));

        //если отправляем через SMTP, то выставляем поле From в корректное значение.
        //иначе отправляем от имени info@вашдомен
        if ($mail->protocol !== 'mail') {
            $mail->setFrom($mail->smtp_username);
        } else {
            $mail->setFrom('info@' . $domain);
        }

        $mail->setSender($domain);
        $mail->setSubject('Сообщение с сайта ' . $domain);

        $mail->setText('Имя отправителя: '
            . $user_name
            . "\r\nТелефон: "
            . $phone_number
            . "\r\nПочта: "
            . $mail_user
            . "\r\nСообщение: "
            . $message);

        $mail->send();

        $this->response->setOutput(json_encode([
            'type'=>'message',
            'text' => 'Ваше сообщение отправлено!',
        ]));

    }
}