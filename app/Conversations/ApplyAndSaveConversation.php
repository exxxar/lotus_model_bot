<?php

namespace App\Conversations;

use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ApplyAndSaveConversation extends Conversation
{
    use CustomConversation;

    protected $bot;

    public function __construct($bot)
    {
        $this->bot = $bot;

    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->loadData();
        $this->askPhone();
    }

    public function askPhone()
    {
        $question = Question::create('Скажие мне свой телефонный номер')
            ->fallback('Спасибо что пообщался со мной:)!');
        $this->ask($question, function (Answer $answer) {
            $vowels = array("(", ")", "-", " ");
            $tmp_phone = $answer->getText()??'';
            $tmp_phone = str_replace($vowels, "", $tmp_phone);
            if (strpos($tmp_phone, "+38") === false)
                $tmp_phone = "+38" . $tmp_phone;

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";
            if (preg_match($pattern, $tmp_phone) === false) {
                $this->bot->reply("Номер введен не верно...\n");
                $this->askPhone();
                return;
            }


                $profile = json_decode($this->bot->userStorage()->get("profile"), true) ?? null;
                if ($profile != null)
                    \App\Profile::create($profile);

                $telegramUser = $this->bot->getUser();
                $id = $telegramUser->getId();

                Telegram::sendMessage([
                    'chat_id' => "-1001176319167",
                    'parse_mode' => 'Markdown',
                    'text' => "Новая анкета:\n"
                        . "*Ф.И.О.*:" . ($profile["full_name"] ?? 'Не указано') . "\n"
                        . "*Возраст:*" . ($profile["age"] ?? 'Не указано') . "\n"
                        . "*Телефон:*" . ($tmp_phone ?? 'Не указано') . "\n"
                        . "*Пол:*" . ($profile["sex"] == 0 ? "Парень" : "Девушка") . "\n"
                        . "*Рост:*" . ($profile["height"] ?? 'Не указано') . "\n"
                        . "*Вес:*" . ($profile["weight"] ?? 'Не указано') . "\n"
                        . "*Объем груди:*" . ($profile["breast_volume"] ?? 'Не указано') . "\n"
                        . "*Объем талии:*" . ($profile["waist"] ?? 'Не указано') . "\n"
                        . "*Объем бёдер:*" . ($profile["hips"] ?? 'Не указано') . "\n"
                        . "*Обучался ранее:*" . ($profile["model_school_education"] == 1 ? "Да" : "Нет") . "\n"
                        . "*Желает обучаться:*" . ($profile["wish_learn"] ?? 0) . "\n"
                        . "*Откуда узнал:*" . ($profile["about"] ?? 'Не указано') . "\n"
                        . "*Образование:*" . ($profile["education"] ?? 'Не указано') . "\n"
                        . "*Размер одежды:*" . ($profile["clothing_size"] ?? 'Не указано') . "\n"
                        . "*Размер обуви:*" . ($profile["shoe_size"] ?? 'Не указано') . "\n"
                        . "*Цвет волос:*" . ($profile["hair_color"] ?? 'Не указано') . "\n"
                        . "*Цвет глаз:*" . ($profile["eye_color"] ?? 'Не указано') . "\n"
                        . "*Хобби:*" . ($profile["hobby"] ?? 'Не указано') . "\n"
                        . "*Город:*" . ($profile["city"] ?? 'Не указано') . "\n"
                    ,
                    'disable_notification' => 'true'
                ]);

                $this->profileMenu("Ваша анкета отправлена в Агенство");

        });
    }
}
