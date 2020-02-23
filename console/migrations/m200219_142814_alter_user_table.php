<?php

use yii\db\Migration;

/**
 * Class m200219_142814_alter_user_table
 */
class m200219_142814_alter_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}','about', $this->text());
        $this->addColumn('{{%user}}','type', $this->integer(3));
        $this->addColumn('{{%user}}','nickname', $this->string(70));
        $this->addColumn('{{%user}}','picture', $this->string());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropDown('{{%user}}','picture');
       $this->dropDown('{{%user}}','nickname');
       $this->dropDown('{{%user}}','type');
       $this->dropDown('{{%user}}','about');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200219_142814_alter_user_table cannot be reverted.\n";

        return false;
    }
    */
}
