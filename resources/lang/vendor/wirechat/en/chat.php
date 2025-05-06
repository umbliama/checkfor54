<?php

return [

    /**-------------------------
     * Чат
     *------------------------*/
    'labels' => [

        'you_replied_to_yourself' => 'Вы ответили себе',
        'participant_replied_to_you' => ':sender ответил(а) Вам',
        'participant_replied_to_themself' => ':sender ответил(а) сам(а) себе',
        'participant_replied_other_participant' => ':sender ответил(а) :receiver',
        'you' => 'Вы',
        'user' => 'Пользователь',
        'replying_to' => 'Ответ :participant',
        'replying_to_yourself' => 'Ответ себе',
        'attachment' => 'Вложение',
    ],

    'inputs' => [
        'message' => [
            'label' => 'Сообщение',
            'placeholder' => 'Введите сообщение',
        ],
        'media' => [
            'label' => 'Медиа',
            'placeholder' => 'Медиа',
        ],
        'files' => [
            'label' => 'Файлы',
            'placeholder' => 'Файлы',
        ],
    ],

    'message_groups' => [
        'today' => 'Сегодня',
        'yesterday' => 'Вчера',

    ],

    'actions' => [
        'open_group_info' => [
            'label' => 'Информация о группе',
        ],
        'open_chat_info' => [
            'label' => 'Информация о чате',
        ],
        'close_chat' => [
            'label' => 'Закрыть чат',
        ],
        'clear_chat' => [
            'label' => 'Очистить историю чата',
            'confirmation_message' => 'Вы уверены, что хотите очистить историю чата? Это удалит только вашу копию и не повлияет на других участников.',
        ],
        'delete_chat' => [
            'label' => 'Удалить чат',
            'confirmation_message' => 'Вы уверены, что хотите удалить этот чат? Он будет удален только у вас и останется у других участников.',
        ],

        'delete_for_everyone' => [
            'label' => 'Удалить для всех',
            'confirmation_message' => 'Вы уверены?',
        ],
        'delete_for_me' => [
            'label' => 'Удалить для меня',
            'confirmation_message' => 'Вы уверены?',
        ],
        'reply' => [
            'label' => 'Ответить',
        ],

        'exit_group' => [
            'label' => 'Покинуть группу',
            'confirmation_message' => 'Вы уверены, что хотите выйти из этой группы?',
        ],
        'upload_file' => [
            'label' => 'Файл',
        ],
        'upload_media' => [
            'label' => 'Фото и видео',
        ],
    ],

    'messages' => [

        'cannot_exit_self_or_private_conversation' => 'Невозможно выйти из личного или приватного диалога',
        'owner_cannot_exit_conversation' => 'Владелец не может выйти из диалога',
        'rate_limit' => 'Слишком много попыток! Пожалуйста, замедлите темп',
        'conversation_not_found' => 'Диалог не найден.',
        'conversation_id_required' => 'Требуется ID диалога',
        'invalid_conversation_input' => 'Некорректный ввод для диалога.',
    ],

    /**-------------------------
     * Компонент информации
     *------------------------*/

    'info' => [
        'heading' => [
            'label' => 'Информация о чате',
        ],
        'actions' => [
            'delete_chat' => [
                'label' => 'Удалить чат',
                'confirmation_message' => 'Вы уверены, что хотите удалить этот чат? Он будет удален только у вас и останется у других участников.',
            ],
        ],
        'messages' => [
            'invalid_conversation_type_error' => 'Разрешены только личные и приватные диалоги',
        ],

    ],

    /**-------------------------
     * Группа
     *------------------------*/

    'group' => [

        // Компонент информации о группе
        'info' => [
            'heading' => [
                'label' => 'Информация о группе',
            ],
            'labels' => [
                'members' => 'Участники',
                'add_description' => 'Добавьте описание группы',
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
                'photo' => [
                    'label' => 'Фото',
                ],
            ],
            'actions' => [
                'delete_group' => [
                    'label' => 'Удалить группу',
                    'confirmation_message' => 'Вы уверены, что хотите удалить эту группу?',
                    'helper_text' => 'Перед удалением группы необходимо удалить всех её участников.',
                ],
                'add_members' => [
                    'label' => 'Добавить участников',
                ],
                'group_permissions' => [
                    'label' => 'Права доступа группы',
                ],
                'exit_group' => [
                    'label' => 'Покинуть группу',
                    'confirmation_message' => 'Вы уверены, что хотите покинуть группу?',
                ],
            ],
            'messages' => [
                'invalid_conversation_type_error' => 'Разрешены только групповые диалоги',
            ],
        ],
        // Компонент участников
        'members' => [
            'heading' => [
                'label' => 'Участники',
            ],
            'inputs' => [
                'search' => [
                    'label' => 'Поиск',
                    'placeholder' => 'Поиск участников',
                ],
            ],
            'labels' => [
                'members' => 'Участники',
                'owner' => 'Владелец',
                'admin' => 'Администратор',
                'no_members_found' => 'Участники не найдены',
            ],
            'actions' => [
                'send_message_to_yourself' => [
                    'label' => 'Написать себе',

                ],
                'send_message_to_member' => [
                    'label' => 'Написать :member',

                ],
                'dismiss_admin' => [
                    'label' => 'Снять с должности админа',
                    'confirmation_message' => 'Вы уверены, что хотите снять :member с должности админа?',
                ],
                'make_admin' => [
                    'label' => 'Назначить админом',
                    'confirmation_message' => 'Вы уверены, что хотите назначить :member администратором?',
                ],
                'remove_from_group' => [
                    'label' => 'Удалить',
                    'confirmation_message' => 'Вы уверены, что хотите удалить :member из этой группы?',
                ],
                'load_more' => [
                    'label' => 'Загрузить ещё',
                ],

            ],
            'messages' => [
                'invalid_conversation_type_error' => 'Разрешены только групповые диалоги',
            ],
        ],
        // Компонент добавления участников
        'add_members' => [
            'heading' => [
                'label' => 'Добавить участников',
            ],
            'inputs' => [
                'search' => [
                    'label' => 'Поиск',
                    'placeholder' => 'Поиск',
                ],
            ],
            'labels' => [

            ],
            'actions' => [
                'save' => [
                    'label' => 'Сохранить',

                ],

            ],
            'messages' => [
                'invalid_conversation_type_error' => 'Разрешены только групповые диалоги',
                'members_limit_error' => 'Число участников не может превышать :count',
                'member_already_exists' => ' Уже добавлен в группу',
            ],
        ],
        // Компонент прав доступа
        'permisssions' => [
            'heading' => [
                'label' => 'Права доступа',
            ],
            'inputs' => [
                'search' => [
                    'label' => 'Поиск',
                    'placeholder' => 'Поиск',
                ],
            ],
            'labels' => [
                'members_can' => 'Участники могут',

            ],
            'actions' => [
                'edit_group_information' => [
                    'label' => 'Редактировать информацию о группе',
                    'helper_text' => 'Сюда входят имя, иконка и описание',
                ],
                'send_messages' => [
                    'label' => 'Отправлять сообщения',
                ],
                'add_other_members' => [
                    'label' => 'Добавлять других участников',
                ],

            ],
            'messages' => [
            ],
        ],

    ],

];