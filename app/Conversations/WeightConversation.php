<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class WeightConversation extends Conversation
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

        $this->getPatternResult("Введите вес","weight","/^[0-9]+$/");

    }
}
