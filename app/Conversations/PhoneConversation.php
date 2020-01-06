<?php

namespace App\Conversations;

use App\Content;
use App\User;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Telegram\Bot\Laravel\Facades\Telegram;

class PhoneConversation extends Conversation
{
    use CustomConversation;

    protected $bot;

    public function __construct($bot, $id)
    {
        $this->bot = $bot;
        $this->course_id = $id;
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askPhone();
    }

    public function askPhone()
    {
        $question = Question::create('Скажите мне ваш телефонный номер')
            ->fallback('Спасибо что пообщался со мной:)!');
        $this->ask($question, function (Answer $answer) {
            $vowels = array("(", ")", "-", " ");
            $tmp_phone = $answer->getText();
            $tmp_phone = str_replace($vowels, "", $tmp_phone);
            if (strpos($tmp_phone, "+38") === false)
                $tmp_phone = "+38" . $tmp_phone;

            $pattern = "/^\+380\d{3}\d{2}\d{2}\d{2}$/";
            if (preg_match($pattern, $tmp_phone) === false) {
                $this->bot->reply("Номер введен не верно...\n");
                $this->askPhone();
                return;
            }

            $telegramUser = $this->bot->getUser();
            $id = $telegramUser->getId();

            $course = Content::find($this->course_id);

            Telegram::sendMessage([
                'chat_id' => "-1001176319167",
                'parse_mode' => 'Markdown',
                'text' => "Заявка на курсы:\n"
                    . "*Имя пользователя:*" . ($user->name ?? $user->name_from_telegram ?? 'Не указано') . "\n"
                    . "*Название курса:*" . ($course->title ?? 'Не указано') . "\n"
                    . "*Цена курса:*" . ($course->price ?? 'Не указано') . "\n"
                ,
                'disable_notification' => 'true'
            ]);

            $this->mainMenu("Заявка отправлена, Вам перезвонят!");


        });
    }
}
