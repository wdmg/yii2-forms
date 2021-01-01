<?php

namespace wdmg\forms\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class InitController extends Controller
{
    /**
     * @inheritdoc
     */
    public $choice = null;

    /**
     * @inheritdoc
     */
    public $defaultAction = 'index';

    public function options($actionID)
    {
        return ['choice', 'color', 'interactive', 'help'];
    }

    public function actionIndex($params = null)
    {
        $version = Yii::$app->controller->module->version;
        $welcome =
            '╔════════════════════════════════════════════════╗'. "\n" .
            '║                                                ║'. "\n" .
            '║              FORMS MODULE, v.'.$version.'             ║'. "\n" .
            '║          by Alexsander Vyshnyvetskyy           ║'. "\n" .
            '║       (c) 2019-2021 W.D.M.Group, Ukraine       ║'. "\n" .
            '║                                                ║'. "\n" .
            '╚════════════════════════════════════════════════╝';
        echo $name = $this->ansiFormat($welcome . "\n\n", Console::FG_GREEN);
        echo "Select the operation you want to perform:\n";
        echo "  1) Apply all module migrations\n";
        echo "  2) Revert all module migrations\n";
        echo "  3) Add demo data\n\n";
        echo "Your choice: ";

        if(!is_null($this->choice))
            $selected = $this->choice;
        else
            $selected = trim(fgets(STDIN));

        if ($selected == "1") {
            Yii::$app->runAction('migrate/up', ['migrationPath' => '@vendor/wdmg/yii2-forms/migrations', 'interactive' => true]);
        } else if($selected == "2") {
            Yii::$app->runAction('migrate/down', ['migrationPath' => '@vendor/wdmg/yii2-forms/migrations', 'interactive' => true]);
        } else if($selected == "3") {

            echo $this->ansiFormat("\n");

            $datetime = date("Y-m-d H:i:sP");
            $formsTable = \wdmg\forms\models\Forms::tableName();
            Yii::$app->db->createCommand()->batchInsert($formsTable, [
                'id', 'source_id', 'name', 'alias', 'title', 'description', 'status', 'locale', 'created_at', 'created_by', 'updated_at', 'updated_by'
            ], [
                [1, null, 'Feedback form', 'feedback-form', 'Feedback', '', 1, 'en-US', $datetime, null, $datetime, null],
                [2, 1, 'Форма обратной связи', 'obratnaya-svyaz', 'Обратная связь', '', 1, 'ru-RU', $datetime, null, $datetime, null],
                [3, 1, 'Форма зворотнього зв`язку', 'zvorotniy-zvyazok', 'Зворотній зв`язок', '', 1, 'uk-UA', $datetime, null, $datetime, null],
                [4, null, 'Callback form', 'сallback-form', 'Callback', '', 1, 'en-US', $datetime, null, $datetime, null],
                [5, 4, 'Форма обратного звонка', 'obratnuy-zvonok', 'Обратный звонок', '', 1, 'ru-RU', $datetime, null, $datetime, null],
                [6, 4, 'Форма зворотнього дзвінка', 'zvorotniy-dzvinok', 'Зворотній дзвінок', '', 1, 'uk-UA', $datetime, null, $datetime, null]
            ])->execute();

            $datetime = date("Y-m-d H:i:sP");
            $fieldsTable = \wdmg\forms\models\Fields::tableName();
            Yii::$app->db->createCommand()->batchInsert($fieldsTable, [
                'id', 'source_id', 'form_id', 'label', 'name', 'placeholder', 'description', 'type', 'sort_order', 'params', 'is_required', 'status', 'locale', 'created_at', 'created_by', 'updated_at', 'updated_by'
            ], [
                [1, NULL, 1, 'Your name', 'your-name', 'Type your name...', '', 1, 10, NULL, 0, 1, 'en-US', $datetime, null, $datetime, null],
                [2, 1, 1, 'Ваше имя', 'your-name', 'Введте ваше имя...', '', 1, 10, NULL, 0, 1, 'ru-RU', $datetime, null, $datetime, null],
                [3, 1, 1, 'Ваше ім`я', 'your-name', 'Введіть ваше ім`я...', '', 1, 10, NULL, 0, 1, 'uk-UA', $datetime, null, $datetime, null],
                [4, NULL, 1, 'Адрес эл. почты', 'your-email', 'Ведите адрес эл. почты...', '', 12, 10, NULL, 1, 1, 'ru-RU', $datetime, null, $datetime, null],
                [5, 4, 1, 'Your e-mail', 'your-email', 'Type your email...', '', 12, 10, NULL, 1, 1, 'en-US', $datetime, null, $datetime, null],
                [6, 4, 1, 'Ареса ел. пошти', 'your-email', 'Введіть адресу ел. пошти...', '', 12, 10, NULL, 1, 1, 'uk-UA', $datetime, null, $datetime, null],
                [7, NULL, 1, 'Контактний телефон', 'your-phone', 'Введіть номер телефону...', '', 16, 10, NULL, 0, 1, 'uk-UA', $datetime, null, $datetime, null],
                [8, 7, 1, 'Your phone', 'your-phone', 'Type your phone number...', '', 16, 10, NULL, 0, 1, 'en-US', $datetime, null, $datetime, null],
                [9, 7, 1, 'Контактный телефон', 'your-phone', 'Введите телефон...', '', 16, 10, NULL, 0, 1, 'ru-RU', $datetime, null, $datetime, null],
                [10, NULL, 1, 'Your message', 'your-message', 'Type your message here...', '', 2, 10, NULL, 1, 1, 'en-US', $datetime, null, $datetime, null],
                [11, 10, 1, 'Ваше сообщение', 'your-message', 'Введите текст Вашего сообщения...', '', 2, 10, NULL, 1, 1, 'ru-RU', $datetime, null, $datetime, null],
                [12, 10, 1, 'Ваше повідомлення', 'your-message', 'Введіть текст Вашого повідомлення...', '', 2, 10, NULL, 1, 1, 'uk-UA', $datetime, null, $datetime, null],
                [13, NULL, 4, 'Your name', 'your-name', 'Type your name...', '', 1, 10, NULL, 0, 1, 'en-US', $datetime, null, $datetime, null],
                [14, 13, 4, 'Ваше имя', 'your-name', 'Введте ваше имя...', '', 1, 10, NULL, 0, 1, 'ru-RU', $datetime, null, $datetime, null],
                [15, 13, 4, 'Ваше ім`я', 'your-name', 'Введіть ваше ім`я...', '', 1, 10, NULL, 0, 1, 'uk-UA', $datetime, null, $datetime, null],
                [16, NULL, 4, 'Your phone', 'your-phone', 'Type your phone number...', '', 16, 10, NULL, 1, 1, 'en-US', $datetime, null, $datetime, null],
                [17, 16, 4, 'Контактный телефон', 'your-phone', 'Введите телефон...', '', 16, 10, NULL, 1, 1, 'ru-RU', $datetime, null, $datetime, null],
                [18, 16, 4, 'Контактний телефон', 'your-phone', 'Введіть номер телефону...', '', 16, 10, NULL, 1, 1, 'uk-UA', $datetime, null, $datetime, null]

            ])->execute();

            echo $this->ansiFormat("Data inserted successfully.\n\n", Console::FG_GREEN);

        } else {
            echo $this->ansiFormat("Error! Your selection has not been recognized.\n\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        echo "\n";
        return ExitCode::OK;
    }
}
