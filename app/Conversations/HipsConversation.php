<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class HipsConversation extends Conversation
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

        $this->getPatternResult("Введите объем бёдер","hips","/^[0-9]+$/");

    }
}
