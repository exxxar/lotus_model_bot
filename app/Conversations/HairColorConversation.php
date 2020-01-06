<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class HairColorConversation extends Conversation
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

        $this->getPatternResult("Ваш цвет волос","hair_color","/[а-яА-Я ]+$/");

        $this->saveData();
    }
}
