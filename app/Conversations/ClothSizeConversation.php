<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class ClothSizeConversation extends Conversation
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

        $this->getPatternResult("Введите размер одежды","clothing_size","/^[0-9a-zA-Z -]+$/");

    }
}
