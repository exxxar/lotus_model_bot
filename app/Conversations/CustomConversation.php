<?php


namespace App\Conversations;


use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Support\Facades\Log;

trait CustomConversation
{
    protected $type;
    protected $item;

    public function getPatternResult($text, $index, $pattern)
    {
        $this->loadData();
        $question = Question::create($text)
            ->fallback('Спасибо что пообщался со мной:)!');
        $this->ask($question, function (Answer $answer) use ($text, $index, $pattern) {
            $result = $answer->getText();
            $matches = [];
            if (preg_match($pattern, $result) == 0) {
                $this->bot->reply("Данные ведены неверно, повторите ввод...\n");
                $this->getPatternResult($text, $index, $pattern);
                return;
            } else {
                $this->item[$index] = $result;

                $this->saveData();

                if ($this->type == 0) {
                    Log::info("Filter start");

                    $this->filterMenu("Данные в фильтре обновлены!");

                    Log::info("Filter end");
                }
                if ($this->type == 1) {
                    Log::info("Profile start");
                    $this->profileMenu("Данные в профиле обновлены!");
                    Log::info("Profile end");

                }
            }
        });
    }

    public function loadData()
    {
        $this->type = $this->bot->userStorage()->get("type")??0;

        Log::info('LOAD TYPE '.$this->type);

        $this->item = json_decode($this->type == 0 ?
            $this->bot->userStorage()->get("filter") :
            $this->bot->userStorage()->get("profile"), true
        );


    }

    public function saveData()
    {
        if ($this->type == 0)
            $this->bot->userStorage()->save([
                'filter' => json_encode($this->item)
            ]);
        else
            $this->bot->userStorage()->save([
                'profile' => json_encode($this->item)
            ]);


    }

    public function profileMenu($message)
    {

        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $profile_data = json_decode($this->bot->userStorage()->get("profile")) ?? null;

        $full_name = $profile_data->full_name ?? null;
        $sex = $profile_data->sex ?? null;
        $height = $profile_data->height ?? null;
        $weight = $profile_data->weight ?? null;
        $age = $profile_data->age ?? null;
        $city = $profile_data->city ?? null;
        $eye_color = $profile_data->eye_color ?? null;
        $hair_color = $profile_data->hair_color ?? null;
        $clothing_size = $profile_data->clothing_size ?? null;
        $shoe_size = $profile_data->shoe_size ?? null;
        $breast_volume = $profile_data->breast_volume ?? null;
        $waist = $profile_data->waist ?? null;
        $hips = $profile_data->hips ?? null;

        $model_school_education = $profile_data->model_school_education ?? null;
        $wish_learn = $profile_data->wish_learn ?? null;
        $education = $profile_data->education ?? null;
        $about = $profile_data->about ?? null;
        $hobby = $profile_data->hobby ?? null;



        $keyboard_main = [
            ["Отправить анкету"],
            ["Ф.И.О." . ($full_name == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Пол" . ($sex == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Рост" . ($height == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Вес" . ($weight == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Возраст" . ($age == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Город проживания" . ($city == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Цвет глаз" . ($eye_color == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Цвет волос" . ($hair_color == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Размер одежды" . ($clothing_size == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Размер обуви" . ($shoe_size == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ];

        $keyboard_women = [
            ["Объем груди" . ($breast_volume == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Объем талии" . ($waist == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Объем бёдер" . ($hips == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ];

        $keyboard_secondary = [
            ["Обучались в модельной школе?" . ($model_school_education == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Желание обучаться" . ($wish_learn == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Ваше образование" . ($education == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Ваше хобби" . ($hobby == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "О вас" . ($about == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ];

        $keyboard_bottom = [
            ["Очистить анкету"],
            ["Главное меню"],
        ];


        if ($sex == null || $sex == 0)
            $keyboard = array_merge($keyboard_main, $keyboard_secondary, $keyboard_bottom);
        else
            if ($sex == 1)
                $keyboard = array_merge($keyboard_main, $keyboard_women, $keyboard_secondary, $keyboard_bottom);

        Log::info("profile 3");

        $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "text" => $message,
                "parse_mode" => "Markdown",
                'reply_markup' => json_encode([
                    'keyboard' => $keyboard,
                    'one_time_keyboard' => false,
                    'resize_keyboard' => true
                ])
            ]);
    }

    public function filterMenu($message)
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();


        $profile_data = json_decode($this->bot->userStorage()->get("filter")) ?? null;


        $full_name = $profile_data->full_name ?? null;
        $sex = $profile_data->sex ?? null;
        $height = $profile_data->height ?? null;
        $weight = $profile_data->weight ?? null;
        $age = $profile_data->age ?? null;
        $eye_color = $profile_data->eye_color ?? null;
        $hair_color = $profile_data->hair_color ?? null;
        $clothing_size = $profile_data->clothing_size ?? null;
        $shoe_size = $profile_data->shoe_size ?? null;
        $breast_volume = $profile_data->breast_volume ?? null;
        $waist = $profile_data->waist ?? null;
        $hips = $profile_data->hips ?? null;


        $keyboard_main = [
            ["Найти моделей"],
            ["Ф.И.О." . ($full_name == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Пол" . ($sex == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Рост" . ($height == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Вес" . ($weight == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Возраст" . ($age == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Цвет глаз" . ($eye_color == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Цвет волос" . ($hair_color == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Размер одежды" . ($clothing_size == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Размер обуви" . ($shoe_size == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ];

        $keyboard_women = [
            ["Объем груди" . ($breast_volume == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Объем талии" . ($waist == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
            ["Объем бёдер" . ($hips == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ];

        $keyboard_bottom = [

            ["Сбросить фильтр"],
            ["Главное меню"],
        ];

        if ($sex == null || $sex == 0)
            $keyboard = array_merge($keyboard_main, $keyboard_bottom);
        else
            if ($sex == 1)
                $keyboard = array_merge($keyboard_main, $keyboard_women, $keyboard_bottom);


        $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "text" => $message,
                "parse_mode" => "Markdown",
                'reply_markup' => json_encode([
                    'keyboard' => $keyboard,
                    'one_time_keyboard' => false,
                    'resize_keyboard' => true
                ])
            ]);
    }

    public function mainMenu( $message)
    {
        $telegramUser = $this->bot->getUser();
        $id = $telegramUser->getId();

        $keyboard = [
            ["	\xF0\x9F\x94\x96Наши услуги"],
            ["\xF0\x9F\x93\x8BАнкета модели"],
            ["\xF0\x9F\x94\x8EПоиск моделей"],
            ["\xF0\x9F\x8C\xBCСписок моделей"],
            ["\xF0\x9F\x93\xB2О нас"],
        ];

        $this->bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "text" => $message,
                "parse_mode" => "Markdown",
                "disable_notification" => true,
                'reply_markup' => json_encode([
                    'keyboard' => $keyboard,
                    'one_time_keyboard' => false,
                    'resize_keyboard' => true
                ])
            ]);
    }
}