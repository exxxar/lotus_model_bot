<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class HeightConversation extends Conversation
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

        $this->getPatternResult("Введите рост","height","/^[0-9]+$/");

    }
}
