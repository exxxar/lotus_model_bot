<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class FullNameConversation extends Conversation
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

        $this->getPatternResult("Полное имя","full_name","/[а-яА-Я0-9 -]+$/");

        $this->saveData();
    }
}
