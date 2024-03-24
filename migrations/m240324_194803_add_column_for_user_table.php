<?php

use yii\db\Migration;

/**
 * Добавить столбец Роль пользователя
 */
class m240324_194803_add_column_for_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'role', $this->smallInteger(10)->notNull()->defaultValue(0)->comment('Роль пользователя'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'role');
    }

}
