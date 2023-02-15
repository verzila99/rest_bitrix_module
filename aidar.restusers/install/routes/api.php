<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\UserTable;

function authenticateError()
{
    $response = new \Bitrix\Main\HttpResponse();
    $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode(['message' => 'You should be logged in']));

    return $response;
}
return function (RoutingConfigurator $routes) {
    $routes->post(
        '/api/login',
        function (HttpRequest $request) {

            $user = new CUser;
            $user = $user->Login(
                $request['login'],
                $request['password'],
            );
            if ($user) {

                return new \Bitrix\Main\Engine\Response\Json([
                    'message' => 'You are logged in',
                ]);
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode(['message' => 'Login or password does not correct']));

            return $response;

        }
    );
    $routes->get(
        '/api/logout',
        function (HttpRequest $request) {
            if (!CUser::GetLogin()) {
                return authenticateError();
            }
            $user = new CUser;
            $user->Logout();
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('200')->setContent(json_encode(['message' => 'You are logged out']));
            return $response;

        }
    );
    $routes->get(
        '/api/users/{id}',
        function ($id) {
            if (!CUser::GetLogin()) {
                return authenticateError();
            }

            $user = CUser::GetByID($id)->fetch();
            $error = '';
            if ($user) {
                return new \Bitrix\Main\Engine\Response\Json([
                    $user,
                ]);
            } else {
                $error = 'User with this id is not found';
            }

            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode(['message' => $error]));

            return $response;

        }
    );
    $routes->post(
        '/api/users/add',
        function (HttpRequest $request) {
            if (!CUser::GetLogin()) {
                return authenticateError();
            }
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
        '/api/users/update',
        function (HttpRequest $request) {
            if (!CUser::GetLogin()) {
                return authenticateError();
            }
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
                    'message' => 'User is updated',
                ]);
            }
            if (!isset($request['id'])) {
                $user->LAST_ERROR = 'id is required';
            }

            if (
                !CUser::GetByID($request['id'])->fetch()
            ) {
                $user->LAST_ERROR = 'User with this id is not found';
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode([
                'message' => $user->LAST_ERROR
            ]));

            return $response;

        }
    );
    $routes->delete(
        '/api/users/delete/{id}',
        function ($id) {
            if (!CUser::GetLogin()) {
                return authenticateError();
            }

            if (CUser::Delete($id)) {
                return new \Bitrix\Main\Engine\Response\Json([
                    'message' => 'User is deleted',
                ]);
            }

            $error = '';
            if (!isset($id)) {
                $error = 'id is required';
            }
            if (
                !UserTable::getList([
                    'filter' => array(
                        'ID' => $id
                    )
                ])->fetch()
            ) {
                $error = 'User with this id is not found';
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode([
                'message' => $error
            ]));

            return $response;

        }
    );
    $routes->post(
        '/api/users/authorize',
        function (HttpRequest $request) {
            if (!CUser::GetLogin()) {
                return authenticateError();
            }
            $error = '';
            if (!isset($request['id'])) {
                $error = 'id is required';
            }
            $user = CUser::GetByID($request['id'])->fetch();
            $groups = CGroup::GetList("c_sort", "asc", [], "N");
            while ($item = $groups->fetch()) {
                $allGroups[] = $item['ID'];
            }
            if ($user && in_array($request['group'], $allGroups)) {
                $arrGroups_new = [$request['group']]; // в какую группу хотим добавить
                $arrGroups_old = CUser::GetUserGroup($request['id']); // получим текущие группы
                $arrGroups = array_unique(array_merge($arrGroups_old, $arrGroups_new)); // объединим два массива и удалим дубли
                $a = new CUser;
                $a->Update($user['ID'], array("GROUP_ID" => $arrGroups)); // обновим профайл пользователя в базе
                $userState = $a->Authorize($user['ID']);
                if ($userState) {
                    return new \Bitrix\Main\Engine\Response\Json([
                        'message' => 'User is authorized',
                        'l' => $allGroups
                    ]);
                }
            } elseif (!in_array($request['group'], $allGroups)) {
                $error = 'Given group is not exist';
            } else {

                $error = 'User with this id is not found';
            }
            $response = new \Bitrix\Main\HttpResponse();
            $response->addHeader('Content-Type', 'application/json')->setStatus('400')->setContent(json_encode([
                'message' => $error
            ]));

            return $response;

        }
    );

};