<?php

namespace App\Http\Controllers\Telegram;

class TelegramBot
{
    /**
     * Токен бота
     * @var string
     */
    protected $token;

    /**
     * URL префикс для бота
     */
    const URL_PREFIX = 'https://api.telegram.org/bot';

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Возвращает путь для запроса
     * @return string
     */
    private function getURL() : string
    {
        return self::URL_PREFIX . $this->token;
    }

    /**
     * @param  array  $params
     * @param  string  $command
     * @return bool|string
     */
    private function send(array $params, string $command)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $this->getURL() . '/' . $command); // адрес api телеграмм
            curl_setopt($curl, CURLOPT_POST, true); // отправка данных методом POST
            curl_setopt($curl, CURLOPT_TIMEOUT, 10); // максимальное время выполнения запроса
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params)); // параметры запроса
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); //возвращает json-ответ
            curl_setopt($curl, CURLOPT_HTTPHEADER, array_merge(array("Content-Type: application/json")));
            $result = curl_exec($curl); // запрос к api
            curl_close($curl);
        }
        catch (\Exception $e) {
            $this->error('Выброшено исключение: ' . $e->getMessage() . "\n");
        }

        return $result;
    }

    /**
     * Отправка сообщения пользователю
     * @param string $chatId
     * @param string $text
     * @param string $parseMode
     * @param array $replyMarkup
     * @return bool|string
     */
    public function sendMessage(string $chatId, string $text, array $replyMarkup = [], bool $inline = false, string $messageId = '', bool $oneTimeKey = true) : string
    {
            $params = [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $text,
                'disable_web_page_preview' => false,
                'reply_markup' => $inline ? json_encode(['inline_keyboard' => $replyMarkup]) : [
                    'one_time_keyboard' => $oneTimeKey,
                    'resize_keyboard' => true,
                    'keyboard' => $replyMarkup
                ],
                'parse_mode' => 'HTML',
            ];

        return $this->send($params, 'sendMessage');
    }

    /**
     * @param  string  $chatId
     * @param  array  $replyMarkup
     * @param  bool  $inline
     * @param  string  $message_id
     * @param  bool  $oneTimeKey
     * @return string
     */
    public function editReplyMarkup(string $chatId, string $text, array $replyMarkup = [], bool $inline = false, string $messageId = '', bool $oneTimeKey = true) : string
    {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'disable_web_page_preview' => false,
            'reply_markup' => $inline ? json_encode(['inline_keyboard' => $replyMarkup]) : [
                'one_time_keyboard' => $oneTimeKey,
                'resize_keyboard' => true,
                'keyboard' => $replyMarkup
            ],
            'parse_mode' => 'HTML',
        ];

        return $this->send($params, 'editMessageReplyMarkup');
    }

    public function deleteMessage(string $chatId, string $messageId)
    {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ];

        return $this->send($params, 'deleteMessage');
    }

}
