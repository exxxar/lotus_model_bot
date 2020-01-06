<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class BreastVolumeConversation extends Conversation
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

        $this->getPatternResult("Объем груди","breast_volume","/^[0-9]+$/");

    }
}
