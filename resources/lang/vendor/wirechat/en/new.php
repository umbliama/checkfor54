<?php

return [

    // Компонент нового чата
    'chat' => [
        'labels' => [
            'heading' => 'Новый чат',
            'you' => 'Вы',

        ],

        'inputs' => [
            'search' => [
                'label' => 'Поиск диалогов',
                'placeholder' => 'Поиск',
            ],
        ],

        'actions' => [
            'new_group' => [
                'label' => 'Новая группа',
            ],

        ],

        'messages' => [

            'empty_search_result' => 'Пользователи, соответствующие вашему запросу, не найдены.',
        ],
    ],

    // Компонент новой группы
    'group' => [
        'labels' => [
            'heading' => 'Новый чат',
            'add_members' => 'Добавить участников',

        ],

        'inputs' => [
            'name' => [
                'label' => 'Название группы',
                'placeholder' => 'Введите название',
            ],
            'description' => [
                'label' => 'Описание',
                'placeholder' => 'Необязательно',
            ],
            'search' => [
                'label' => 'Поиск',
                'placeholder' => 'Поиск',
            ],
            'photo' => [
                'label' => 'Фото',
            ],
        ],

        'actions' => [
            'cancel' => [
                'label' => 'Отмена',
            ],
            'next' => [
                'label' => 'Далее',
            ],
            'create' => [
                'label' => 'Создать',
            ],

        ],

        'messages' => [
            'members_limit_error' => 'Число участников не может превышать :count',
            'empty_search_result' => 'Пользователи, соответствующие вашему запросу, не найдены.',
        ],
    ],

];