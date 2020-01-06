<?php

namespace App\Http\Controllers;

use App\Conversations\AboutConversation;
use App\Conversations\AgeConversation;
use App\Conversations\ApplyAndSaveConversation;
use App\Conversations\BreastVolumeConversation;
use App\Conversations\CityConversation;
use App\Conversations\ClothSizeConversation;
use App\Conversations\EducationConversation;
use App\Conversations\EyeColorConversation;
use App\Conversations\FullNameConversation;
use App\Conversations\HairColorConversation;
use App\Conversations\HeightConversation;
use App\Conversations\HipsConversation;
use App\Conversations\HobbyConversation;
use App\Conversations\PhoneConversation;
use App\Conversations\ShoeSizeConversation;
use App\Conversations\WaistConversation;
use App\Conversations\WeightConversation;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    public function ageConversation(BotMan $bot)
    {
        $bot->startConversation(new AgeConversation($bot));
    }

    public function cityConversation(BotMan $bot)
    {
        $bot->startConversation(new CityConversation($bot));
    }

    public function hobbyConversation(BotMan $bot)
    {
        $bot->startConversation(new HobbyConversation($bot));
    }

    public function weightConversation(BotMan $bot)
    {
        $bot->startConversation(new WeightConversation($bot));
    }

    public function waistConversation(BotMan $bot)
    {
        $bot->startConversation(new WaistConversation($bot));
    }

    public function shoeSizeConversation(BotMan $bot)
    {
        $bot->startConversation(new ShoeSizeConversation($bot));
    }

    public function applyAndSaveConversation(BotMan $bot)
    {
        $bot->startConversation(new ApplyAndSaveConversation($bot));
    }

    public function phoneConversation(BotMan $bot,$id)
    {
        $bot->startConversation(new PhoneConversation($bot,$id));
    }

    public function hipsConversation(BotMan $bot)
    {
        $bot->startConversation(new HipsConversation($bot));
    }

    public function hairColorConversation(BotMan $bot)
    {
        $bot->startConversation(new HairColorConversation($bot));
    }

    public function fullNameConversation(BotMan $bot)
    {
        $bot->startConversation(new FullNameConversation($bot));
    }

    public function eyeColorConversation(BotMan $bot)
    {
        $bot->startConversation(new EyeColorConversation($bot));
    }

    public function educationConversation(BotMan $bot)
    {
        $bot->startConversation(new EducationConversation($bot));
    }

    public function clothSizeConversation(BotMan $bot)
    {
        $bot->startConversation(new ClothSizeConversation($bot));
    }

    public function breastVolumeConversation(BotMan $bot)
    {
        $bot->startConversation(new BreastVolumeConversation($bot));
    }

    public function aboutConversation(BotMan $bot)
    {
        $bot->startConversation(new AboutConversation($bot));
    }

    public function heightConversation(BotMan $bot)
    {
        $bot->startConversation(new HeightConversation($bot));
    }

}
