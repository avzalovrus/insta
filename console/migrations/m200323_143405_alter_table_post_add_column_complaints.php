<?php

use yii\db\Migration;

/**
 * Class m200323_143405_alter_table_post_add_column_complaints
 */
class m200323_143405_alter_table_post_add_column_complaints extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {   //Добавляем поле complaintsв таблицу post
        $this->addColumn('{{%post}}', 'complaints', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%post}}', 'complaints');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200323_143405_alter_table_post_add_column_complaints cannot be reverted.\n";

        return false;
    }
    */
}
