<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\UserTable;

return function (RoutingConfigurator $routes) {
    $routes->get(
        '/users/{id}',
        function ($id) {

            $user = CUser::GetByID($id);

            if ($user->Fetch()) {
                return new \Bitrix\Main\Engine\Response\Json([
                    $user->Fetch(),
                ]);
            } else {
                $user->LAST_ERROR = 'Пользователь с таким id не найден';
            }

            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode(['message' => $user->LAST_ERROR]));

            return $response;


        }
    );
    $routes->post(
        '/users/add',
        function (HttpRequest $request) {
            $user = new CUser;
            $id = $user->Add([
                "NAME" => $request['name'],
                "LAST_NAME" => $request['last_name'],
                'EMAIL' => $request['email'],
                'LOGIN' => $request['login'],
                'PASSWORD' => $request['password'],

            ]);
            if ($id > 0) {

                return new \Bitrix\Main\Engine\Response\Json([
                    'id' => $id,
                ]);
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode(['message' => $user->LAST_ERROR]));

            return $response;

        }
    );

    $routes->put(
        '/users/update',
        function (HttpRequest $request) {

            $user = new CUser;
            $fields = [
                "NAME" => $request['name'],
                "LAST_NAME" => $request['last_name'],
                'EMAIL' => $request['email'],
                'LOGIN' => $request['login'],
                'PASSWORD' => $request['password'],
            ];

            $state = $user->Update($request['id'], $fields);
            if ($state) {
                return new \Bitrix\Main\Engine\Response\Json([
                    'message' => 'Пользователь успешно изменен!',
                ]);
            }
            if (!isset($request['id'])) {
                $user->LAST_ERROR = 'id отсутствует';
            }

            if (
                !CUser::GetByID($request['id'])->fetch()
            ) {
                $user->LAST_ERROR = 'Пользователь с таким id не найден';
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode([
                'message' => $user->LAST_ERROR
            ]));

            return $response;

        }
    );
    $routes->delete(
        '/users/delete/{id}',
        function ($id) {


            if (CUser::Delete($id)) {
                return new \Bitrix\Main\Engine\Response\Json([
                    'message' => 'Пользователь успешно удалён!',
                ]);
            }

            $error = '';
            if (!isset($id)) {
                $error = 'id отсутствует';
            }
            if (
                !UserTable::getList([
                    'filter' => array(
                        'ID' => $id
                    )
                ])->fetch()
            ) {
                $error = 'Пользователь с таким id не найден';
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode([
                'message' => $error
            ]));

            return $response;

        }
    );
    $routes->post(
        '/users/authorize',
        function (HttpRequest $request) {
            $error = '';
            if (!isset($request['id'])) {
                $error = 'id отсутствует';
            }
            $user = CUser::GetByID($request['id']);

            if ($user->fetch()) {
                $arrGroups_new = $request['groups']; // в какие группы хотим добавить
                $arrGroups_old = $user->GetUserGroupArray(); // получим текущие группы
                $arrGroups = array_unique(array_merge($arrGroups_old, $arrGroups_new)); // объединим два массива и удалим дубли
                $user->Update($user->GetID(), array("GROUP_ID" => $arrGroups)); // обновим профайл пользователя в базе
                $user->Authorize($user->GetID());
                if ($user->Authorize($user->GetID())) {
                    return new \Bitrix\Main\Engine\Response\Json([
                        'message' => 'Пользователь успешно авторизован!',
                    ]);
                }
            } else {

                $error = 'Пользователь с таким id не найден';
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode([
                'message' => $error
            ]));

            return $response;

        }
    );

};