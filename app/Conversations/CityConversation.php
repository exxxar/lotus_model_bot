<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class CityConversation extends Conversation
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

        $this->getPatternResult("Из какого вы города?","city","/[а-яА-Я0-9]+$/");

        $this->saveData();
    }
}
