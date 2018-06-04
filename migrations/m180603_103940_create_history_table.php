<?php

use yii\db\Migration;

/**
 * Handles the creation of table `history`.
 */
class m180603_103940_create_history_table extends Migration
{
   public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%history}}', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->notNull(),
            'receiver_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(10,2)->notNull()->defaultValue(0),
            'realized' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
 
        $this->addForeignKey(
            'fk-user-sender_id',
            'history',
            'sender_id',
            'user',
            'id'
        );     
        $this->addForeignKey(
            'fk-user-receiver_id',
            'history',
            'receiver_id',
            'user',
            'id'
        );     
    }
 
    public function down()
    {
        $this->dropTable('{{%history}}');
    }
}
