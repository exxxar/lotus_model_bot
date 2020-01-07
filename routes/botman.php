<?php

use App\Content;
use App\Http\Controllers\BotManController;
use App\User;
use Illuminate\Support\Facades\Log;

$botman = resolve('botman');


function createUser($bot)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();
    $username = $telegramUser->getUsername();
    $lastName = $telegramUser->getLastName();
    $firstName = $telegramUser->getFirstName();

    $user = User::where("email", "$id@t.me")->first();
    if ($user == null)
        $user = \App\User::create([
            'name' => $username ?? "$id",
            'email' => "$id@t.me",
            'password' => bcrypt($id),
            'name_from_telegram' => "$lastName $firstName",
            'telegram_chat_id' => $id,
            'phone' => '',
        ]);
    return $user;
}

function getDataFromApi($bot, $text = "")
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $query = "text=$text";

    try {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
                'content' => $query
            ),
        ));
        ini_set('max_execution_time', 1000000);
        $content = file_get_contents(
            $file = 'http://lotus-model.ru/search-models.php',
            $use_include_path = false,
            $context);
        ini_set('max_execution_time', 60);
    } catch (ErrorException $e) {
        $content = [];
    }
    return json_decode($content);
}

function mainMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        ["	\xF0\x9F\x94\x96Наши услуги"],
        ["\xF0\x9F\x93\x8BАнкета модели"],
        ["\xF0\x9F\x94\x8EПоиск моделей"],
        ["\xF0\x9F\x8C\xBCСписок моделей"],
        ["\xF0\x9F\x93\xB2О нас"],
    ];

    $bot->sendRequest("sendMessage",
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

function profileMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $profile_data = json_decode($bot->userStorage()->get("profile")) ?? null;

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
        ["Ф.И.О.\xE2\x9D\x97" . ($full_name == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Пол" . ($sex == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ["Рост" . ($height == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Вес" . ($weight == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ["Возраст\xE2\x9D\x97" . ($age == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Город проживания" . ($city == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
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
        ["Главное меню"],
    ];


    if ($sex == null || $sex == 0)
        $keyboard = array_merge($keyboard_main, $keyboard_secondary, $keyboard_bottom);
    else
        if ($sex == 1)
            $keyboard = array_merge($keyboard_main, $keyboard_women, $keyboard_secondary, $keyboard_bottom);


    $bot->sendRequest("sendMessage",
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

function filterMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $profile_data = json_decode($bot->userStorage()->get("filter")) ?? null;

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


    $keyboard_main = [
        ["Найти моделей"],
        ["Ф.И.О.\xE2\x9D\x97" . ($full_name == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Пол" . ($sex == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ["Рост" . ($height == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Вес" . ($weight == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
        ["Возраст\xE2\x9D\x97" . ($age == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85"), "Город проживания" . ($city == null ? "\xE2\x9D\x8E" : "\xE2\x9C\x85")],
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


    $bot->sendRequest("sendMessage",
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

function modelsMenu($bot, $message)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        ["Девушки", "Парни", "Дети"],
        ["Главное меню"],
    ];

    $bot->sendRequest("sendMessage",
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


$botman->hears('Ф.И.О.*', BotManController::class . "@fullNameConversation");
$botman->hears('Рост.*', BotManController::class . "@heightConversation");
$botman->hears('Вес.*', BotManController::class . "@weightConversation");
$botman->hears('Возраст.*', BotManController::class . "@ageConversation");
$botman->hears('Цвет глаз.*', BotManController::class . "@eyeColorConversation");
$botman->hears('Цвет волос.*', BotManController::class . "@hairColorConversation");
$botman->hears('Город проживания.*', BotManController::class . "@cityConversation");
$botman->hears('Размер одежды.*', BotManController::class . "@clothSizeConversation");
$botman->hears('Размер обуви.*', BotManController::class . "@shoeSizeConversation");
$botman->hears('Объем груди.*', BotManController::class . "@breastVolumeConversation");
$botman->hears('Объем талии.*', BotManController::class . "@waistConversation");
$botman->hears('Объем бёдер.*', BotManController::class . "@hipsConversation");
$botman->hears('Ваше хобби.*', BotManController::class . "@hobbyConversation");
$botman->hears('О вас.*', BotManController::class . "@aboutConversation");
$botman->hears('Ваше образование.*', BotManController::class . "@educationConversation");
$botman->hears('Отправить анкету', BotManController::class . "@applyAndSaveConversation");
$botman->hears('/request ([0-9]+)', BotManController::class . "@phoneConversation");

$botman->hears('Сбросить фильтр', function ($bot) {
    $bot->userStorage()->save([
        "filter" => json_encode([])
    ]);
    filterMenu($bot, "Вы сбросили фильтр");
});

$botman->hears('Сбросить анкету', function ($bot) {
    $bot->userStorage()->save([
        "profile" => json_encode([])
    ]);
    filterMenu($bot, "Вы очистили свою анкету");
});

$botman->hears('/search|.*Поиск моделей', function ($bot) {
    $bot->userStorage()->save([
        'type' => 0//search
    ]);
    filterMenu($bot, "Найдите интересующую вас модель");
});

$botman->hears('/profile|.*Анкета модели', function ($bot) {
    $bot->userStorage()->save([
        'type' => 1//profile
    ]);
    profileMenu($bot, "Заполните анкету модели");
});

$botman->hears('/start|.*Главное меню', function ($bot) {
    mainMenu($bot, "Lotus Model Agency | Lotus Kids");
})->stopsConversation();

$botman->hears('/models|.*Список моделей', function ($bot) {
    modelsMenu($bot, "Список моделей по категориям");
})->stopsConversation();

function getModelsByCategory($bot, $cat, $page)
{

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $index = (["Дети" => 12, "Парни" => 10, "Девушки" => 11])[$cat];

    $data = getDataFromApi($bot);

    $result = array_filter($data, function ($var) use ($index) {
        return $var->parent == $index;
    });

    $result = array_slice($result, $page * 10, min(max(count($result) - $page * 10, 0), 10));

    ini_set('max_execution_time', 1000000);
    foreach ($result as $key => $model) {

        $keyboard = [
            [
                ['text' => "\xF0\x9F\x83\x8FИнформация о модели", 'callback_data' => "/model_info " . $model->id]
            ]
        ];

        $bot->sendRequest("sendPhoto",
            [
                "chat_id" => "$id",
                "photo" => "http://lotus-model.ru/" . $model->user_data->main_photo,
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);
    }

    $keyboard = [];

    if ($page == 0 && count($result) == 10)
        array_push($keyboard, [
            ['text' => "\xE2\x8F\xA9Следующая страница", 'callback_data' => "/all_models " . ($page + 1) . " " . $cat]
        ]);
    if ($page != 0 && count($result) == 10)
        array_push($keyboard, [
            ['text' => "\xE2\x8F\xAAПредидушая страница", 'callback_data' => "/all_models " . ($page - 1) . " " . $cat],
            ['text' => "\xE2\x8F\xA9Следующая страница", 'callback_data' => "/all_models " . ($page + 1) . " " . $cat]
        ]);
    if ($page != 0 && count($result) < 10)
        array_push($keyboard, [
            ['text' => "\xE2\x8F\xAAСледующая страница", 'callback_data' => "/all_models " . ($page - 1) . " " . $cat],
        ]);

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Ваши действия",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])
        ]);
    ini_set('max_execution_time', 60);

}

function getModelsById($bot, $modelId)
{

    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $data = getDataFromApi($bot);

    $result = array_filter($data, function ($var) use ($modelId) {
        return $var->id == $modelId;
    });

    $result = json_decode(json_encode(array_pop($result)));


    $keyboard = [
        [
            ['text' => "Профиль на сайте", "url" => "http://lotus-model.ru/" . $result->uri],

        ],
    ];

    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => "http://lotus-model.ru/" . $result->user_data->main_photo,
            "parse_mode" => "Markdown",
            "caption" => "*" . $result->username . "*\n"
                . "*Возраст:*_" . $result->user_data->age . "_\n"
                . "*Рост:*_" . $result->user_data->height . "_\n"
                . "*Параметры:*_" . $result->user_data->parameters . "_\n"
                . "*Цвет глаз:*_" . $result->user_data->eye_color . "_\n"
                . "*Цвет волос:*_" . $result->user_data->hair_color . "_\n"
                . "*Размер одежды:*_" . $result->user_data->clothing_size . "_\n"
                . "*Размер обуви:*_" . $result->user_data->shoe_size . "_\n"
            ,
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])

        ]);

}

$botman->hears('(Дети|Парни|Девушки)', function ($bot, $cat) {
    getModelsByCategory($bot, $cat, 0);
});

$botman->hears('/all_models ([0-9]+) (Дети|Парни|Девушки)', function ($bot, $page, $cat) {
    getModelsByCategory($bot, $cat, $page);
});

$botman->hears('/model_info ([0-9]+)', function ($bot, $modelId) {
    getModelsById($bot, $modelId);
});

$botman->hears('.*Наши услуги', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Обучение для взрослых", "callback_data" => "/basic"],
        ],
        [
            ['text' => "Обучение для детей", "callback_data" => "/child"],
        ],
        [
            ['text' => "Другие услуги", "url" => "https://telegra.ph/Uslugi-01-06"],
        ],

    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Мы в соц. сетях",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);
});

function getContentByType($bot, $type)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $content = \App\Content::where("type", $type)->get();

    foreach ($content as $item) {
        $keyboard = [
            [
                ['text' => "Подробнее", "callback_data" => "/info " . $item->id],
                ['text' => "Записаться\xE2\x98\x9D", "callback_data" => "/request " . $item->id],
            ],

        ];

        $bot->sendRequest("sendPhoto",
            [
                "chat_id" => "$id",
                "photo" => $item->image,
                "parse_mode" => "Markdown",
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])

            ]);
    }
}

$botman->hears('/basic', function ($bot) {
    getContentByType($bot, 1);
});

$botman->hears('/child', function ($bot) {
    getContentByType($bot, 0);
});

function applyCustomFilter($bot, $page)
{
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $profile_data = json_decode($bot->userStorage()->get("filter")) ?? null;

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

    $result = getDataFromApi($bot);

    if ($full_name!=null)
        $result = array_filter($result, function ($var) use ($full_name) {
            return strpos($var->username, $full_name) !== false;
        });

    if ($sex!=null)
        $result = array_filter($result, function ($var) use ($sex) {
            return $var->parent == ($sex == 0 ? 10 : 11);
        });

    if ($height!=null)
        $result = array_filter($result, function ($var) use ($height) {
            return intval($var->user_data->height) == intval($height);
        });

    if ($weight!=null)
        $result = array_filter($result, function ($var) use ($weight) {
            return intval($var->user_data->weight) == intval($weight);
        });

    if ($age!=null)
        $result = array_filter($result, function ($var) use ($age) {
            return intval($var->user_data->age) == intval($age);
        });

    if ($breast_volume!=null || $waist!=null || $hips!=null)
        $result = array_filter($result, function ($var) use ($breast_volume, $waist, $hips) {
            return strpos($var->user_data->params, $hips) !== false ||
                strpos($var->user_data->params, $waist) !== false ||
                strpos($var->user_data->params, $hips) !== false;
        });

    if ($eye_color!=null)
        $result = array_filter($result, function ($var) use ($eye_color) {
            return substr($var->user_data->eye_color, $eye_color) !== false;
        });

    if ($clothing_size!=null)
        $result = array_filter($result, function ($var) use ($clothing_size) {
            return substr($var->user_data->clothing_size, $clothing_size) !== false;
        });

    if ($hair_color!=null)
        $result = array_filter($result, function ($var) use ($hair_color) {
            return substr($var->user_data->hair_color, $hair_color) !== false;
        });

    if ($shoe_size!=null)
        $result = array_filter($result, function ($var) use ($shoe_size) {
            return substr($var->user_data->shoe_size, $shoe_size) !== false;
        });


    $result = array_slice($result, $page * 10, min(max(count($result) - $page * 10, 0), 10));

    if (count($result)!=0) {
        ini_set('max_execution_time', 1000000);
        foreach ($result as $key => $model) {

            $keyboard = [
                [
                    ['text' => "\xF0\x9F\x83\x8FИнформация о модели", 'callback_data' => "/model_info " . $model->id]
                ]
            ];

            $bot->sendRequest("sendPhoto",
                [
                    "chat_id" => "$id",
                    "photo" => "http://lotus-model.ru/" . $model->user_data->main_photo,
                    'reply_markup' => json_encode([
                        'inline_keyboard' =>
                            $keyboard
                    ])
                ]);
        }
    }
    else
        $bot->reply("Нет подходящих моделей!");

    $keyboard = [];

    if ($page == 0 && count($result) == 10)
        array_push($keyboard, [
            ['text' => "\xE2\x8F\xA9Следующая страница", 'callback_data' => "/search_models " . ($page + 1)]
        ]);
    if ($page != 0 && count($result) == 10)
        array_push($keyboard, [
            ['text' => "\xE2\x8F\xAAПредидушая страница", 'callback_data' => "/search_models " . ($page - 1)],
            ['text' => "\xE2\x8F\xA9Следующая страница", 'callback_data' => "/search_models " . ($page + 1)]
        ]);
    if ($page != 0 && count($result) < 10)
        array_push($keyboard, [
            ['text' => "\xE2\x8F\xAAСледующая страница", 'callback_data' => "/search_models " . ($page - 1)],
        ]);

    if (count($keyboard) > 0)
        $bot->sendRequest("sendMessage",
            [
                "chat_id" => "$id",
                "text" => "Ваши действия",
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])
            ]);
    ini_set('max_execution_time', 60);
}

$botman->hears('/search_models ([0-9]+)', function ($bot, $page) {
    applyCustomFilter($bot, $page);
});

$botman->hears('Найти моделей', function ($bot) {
    applyCustomFilter($bot, 0);
});

$botman->hears("/info ([0-9]+)", function ($bot, $courseId) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $content = Content::find($courseId);


    $keyboard = [
        [
            ['text' => "Записаться\xE2\x98\x9D", "callback_data" => "/request " . $content->id],
        ],

    ];


    $bot->sendRequest("sendPhoto",
        [
            "chat_id" => "$id",
            "photo" => $content->image,
            "parse_mode" => "Markdown",
            "caption" =>
                "*" . $content->title . "*\n"
                . "*Цена*:" . $content->price,

        ]);

    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "_" . $content->description . "_",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' =>
                    $keyboard
            ])

        ]);

});

$botman->hears('.*О нас', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Наш сайт", "url" => "http://lotus-model.ru"],
            ['text' => "Мы в Instagram", "url" => "https://www.instagram.com/lotus_model_agency/"],
            ['text' => "Мы в Vkontake", "url" => "https://vk.com/lotus_model_agency"],
        ],
        [
            ['text' => "Получай скидки на обучение и не только!", "url" => "https://t.me/skidki_dn_bot"],
        ]

    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Мы в соц. сетях",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);
});

/*$botman->fallback(function ($bot) {
    $bot->reply('Данная возможность еще в разработке!');
});*/


$botman->hears('Обучались в модельной школе?.*', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Да,обучался\xF0\x9F\x91\x8D", "callback_data" => "/set_profile 1 model_school_education"],
            ['text' => "Нет, не обучался\xF0\x9F\x91\x8E", "callback_data" => "/set_profile 0 model_school_education"],
        ],

    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Укажите своё желание обучаться",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);
});
$botman->hears('Пол.*', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Парень\xF0\x9F\x91\xA6", "callback_data" => "/set_profile 0 sex"],
            ['text' => "Девушка\xF0\x9F\x91\xA7", "callback_data" => "/set_profile 1 sex"],
        ],

    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Укажите свой пол",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);
});
$botman->hears('Желание обучаться.*', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    $keyboard = [
        [
            ['text' => "Хочу обучаться\xF0\x9F\x91\x8D", "callback_data" => "/set_profile 1 wish_learn"],
            ['text' => "Не хочу обучаться\xF0\x9F\x91\x8E", "callback_data" => "/set_profile 0 wish_learn"],
        ],

    ];
    $bot->sendRequest("sendMessage",
        [
            "chat_id" => "$id",
            "text" => "Укажите своё желание обучаться",
            "parse_mode" => "Markdown",
            'reply_markup' => json_encode([
                'inline_keyboard' => $keyboard,
            ])
        ]);
});

$botman->hears('/set_profile ([0-9]+) ([0-9a-zA-Z _]+)', function ($bot, $value, $index) {
    Log::info("step1");
    $type = $bot->userStorage()->get("type");

    Log::info($index . "=" . $value);
    $item = json_decode($type == 0 ?
        $bot->userStorage()->get("filter") :
        $bot->userStorage()->get("profile"), true
    );

    $item[$index] = $value;

    Log::info(print_r($item, true));

    if ($type == 0) {
        $bot->userStorage()->save([
            'filter' => json_encode($item)
        ]);
        filterMenu($bot, "Данные в фильтре обновлены!");
    } else {
        $bot->userStorage()->save([
            'profile' => json_encode($item)
        ]);
        profileMenu($bot, "Данные в профиле обновлены!");
    }
});

