<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class WaistConversation extends Conversation
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

        $this->getPatternResult("Введите объем талии","waist","/^[0-9]+$/");

    }
}
