<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\User;
use DateTime;
use Faker\Factory;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class UserController extends Controller
{
    /**
     * @return void
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function actionCreateUsers(): void
    {
        $faker = Factory::create();
        $password = '12345';
        $dateTime = (new DateTime())->getTimestamp();

        $data = [];

        // Создание Админа
        $data[] = [
            'username' => 'admin',
            'auth_key' => Yii::$app->security->generateRandomString(),
            'password_hash' => Yii::$app->security->generatePasswordHash($password),
            'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
            'email' => $faker->email,
            'status' => User::STATUS_ACTIVE,
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
            'verification_token' => Yii::$app->security->generateRandomString() . '_' . time(),
            'role' => User::Role_Admin
        ];

        // Создание Менеджеров
        for ($i = 1; $i <= 2; $i++) {
            $data[] = [
                'username' => 'manager' . $i,
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => Yii::$app->security->generatePasswordHash($password),
                'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'email' => $faker->email,
                'status' => User::STATUS_ACTIVE,
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
                'verification_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'role' => User::Role_Manager
            ];
        }

        // Создание Юзеров
        for ($i = 1; $i <= 2; $i++) {
            $data[] = [
                'username' => 'user' . $i,
                'auth_key' => Yii::$app->security->generateRandomString(),
                'password_hash' => Yii::$app->security->generatePasswordHash($password),
                'password_reset_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'email' => $faker->email,
                'status' => User::STATUS_ACTIVE,
                'created_at' => $dateTime,
                'updated_at' => $dateTime,
                'verification_token' => Yii::$app->security->generateRandomString() . '_' . time(),
                'role' => User::Role_User
            ];
        }

        Yii::$app->db->createCommand()->batchInsert('user', [
            'username',
            'auth_key',
            'password_hash',
            'password_reset_token',
            'email',
            'status',
            'created_at',
            'updated_at',
            'verification_token',
            'role'
        ], $data)->execute();

        $this->stdout("Users create!!!\n", Console::FG_GREEN);

    }
}