<?php

use App\Http\Controllers\BotManController;
use App\User;
use Illuminate\Support\Facades\Log;

$botman = resolve('botman');

const MENU = [
    [
        "id" => "0",
        "title" => "Мужской курс \"LONDОN\"",
        "price" => "2 500 ₽",
        "image" => "https://sun9-38.userapi.com/c850728/v850728456/c8164/0NrWXJh-07o.jpg",
        "description" =>
            'курс "LONDОN" для парней от 14 до 25 лет
-курс длится 3 месяца
-в курс входят 7 дисциплин
-занятия проходят 2-3 раза в неделю
-длительность 1-ого занятия 2 часа
-стоимость обучения 2500 руб./мес.
-на протяжении всего обучения (по желанию) Вас привлекают к участию в различных мероприятиях (в зависимости от требований заказчика)
-по окончанию обучения модель получает диплом на 2-х языках и именную карту со скидками от партнеров агенства
-выпускной проходит в виде fashion показа
-по Вашему желанию менеджер Lotus Model Agency подбирает наиболее подходящий контракт для работы за границей
'
    ],
    [
        "id" => "1",
        "title" => "Курс \"Paris\"",
        "price" => "4 000 ₽",
        "image" => "https://sun9-51.userapi.com/c846322/v846322097/1b3561/ZYbm1mFqf5g.jpg",
        "description" =>
            'Курс "Paris" для девушек от 14 до 25 лет
-курс длится 3 месяца
-в курс входят 15 предметов
-занятия проходят 5 раз в неделю
-длительность 1-ого занятия 2 часа
-стоимость обучения 4000 руб./мес.
-на протяжении всего обучения (по желанию) Вас привлекают к участию в различных мероприятиях (в зависимости от требований заказчика)
-по окончанию обучения модель получает диплом на 2-х языках
-выпускной проходит в виде fashion показа
-по Вашему желанию менеджер Lotus Model Agency подбирает наиболее подходящий контракт для работы за границей
'
    ],
    [
        "id" => "2",
        "title" => "Курс \"Moscow\"",
        "price" => "2 500 ₽",
        "image" => "https://sun9-43.userapi.com/c846121/v846121097/1ab209/gP6gO_HXELo.jpg",
        "description" => 'Курс "Moscow" (базовый курс) для девушек от 14 до 25 лет
-курс длится 3 месяца
-занятия проходят 2 раза в неделю
-длительность 1-ого занятия 2 часа
-стоимость обучения 2500 руб./мес.
-на протяжении всего обучения (по желанию) Вас привлекают к участию в различных мероприятиях (в зависимости от требований заказчика)
-по окончанию обучения модель получает диплом на 2-х языках
-выпускной проходит в виде fashion показа
-по Вашему желанию менеджер Lotus Model Agency подбирает наиболее подходящий контракт для работы за границей
'
    ],
    [
        "id" => "3",
        "title" => "Курс \"INDIVIDUAL\"",
        "price" => "3 000 ₽",
        "image" => "https://sun9-30.userapi.com/c850236/v850236097/f7ed4/zFdK31DYVqw.jpg",
        "description" => '-количество занятий на ваш выбор из перечня - не менее 3
-длительность 1-ого занятия 2 часа
-на протяжении всего обучения (по желанию) Вас привлекают к участию в различных мероприятиях (в зависимости от требований заказчика)
-по окончанию обучения модель получает диплом на 2-х языках
-выпускной проходит в виде fashion показа
-по Вашему желанию менеджер Lotus Model Agency подбирает наиболее подходящий контракт для работы за границей
'
    ],
    [
        "id" => "4",
        "title" => "Курс \"Singapore\"",
        "price" => "3 000 ₽",
        "image" => "https://sun9-68.userapi.com/c852128/v852128580/1df45f/n2nVNUq90e4.jpg",
        "description" => 'Курс "Singapore" для девушек от 13 до 25 лет
-курс длится 9 месяцев
-в курс входят 18 дисциплин
-занятия проходят 5 раза в неделю
-длительность 1-ого занятия 2 часа
-стоимость обучения 3000 руб./мес.
-на протяжении всего обучения (по желанию) Вас привлекают к участию в различных мероприятиях (в зависимости от требований заказчика)
-по окончанию обучения модель получает диплом на 2-х языках
-выпускной проходит в виде fashion показа
-по Вашему желанию менеджер Lotus Model Agency подбирает наиболее подходящий контракт для работы за границей
'
    ],
];

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

$botman->hears('Сбросить фильтр', function ($bot) {
    $bot->userStorage("filter")->delete();
    filterMenu($bot, "Вы сбросили фильтр");
});

$botman->hears('Сбросить анкету', function ($bot) {
    $bot->userStorage("profile")->delete();
    filterMenu($bot, "Вы очистил");
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
});

$botman->hears('/models|.*Список моделей', function ($bot) {
    modelsMenu($bot, "Список моделей по категориям");
});

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
            ['text' => "Другие услуги", "callback_data" => "/other"],
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

$botman->hears('/basic', function ($bot) {
    $telegramUser = $bot->getUser();
    $id = $telegramUser->getId();

    foreach (MENU as $item) {
        $keyboard = [
            [
                ['text' => "Записаться на курс\xE2\x98\x9D", "callback_data" => "/request ".$item["id"]],
            ],

        ];

        $bot->sendRequest("sendPhoto",
            [
                "chat_id" => "$id",
                "photo" => $item["image"],
                "parse_mode" => "Markdown",
                "caption" => "*Цена*:" . $item["price"] . "\n_".$item["description"]."_",
                'reply_markup' => json_encode([
                    'inline_keyboard' =>
                        $keyboard
                ])

            ]);
    }
});

$botman->hears('/request ([0-9]+)', function ($bot) {
    $bot->reply("Раздел в разработке");
});

$botman->hears('Отправить анкету', function ($bot) {
    $bot->reply("Раздел в разработке");
});

$botman->hears('Найти моделей', function ($bot) {
    $bot->reply("Раздел в разработке");
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

$botman->fallback(function($bot) {
    $bot->reply('Данная возможность еще в разработке!');
});