<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180602_174731_create_user_table extends Migration
{
   public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
 
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'balance' => $this->decimal(10,2)->notNull()->defaultValue(0),
        ], $tableOptions);
 
        $this->createIndex('idx-user-username', '{{%user}}', 'username');
        
//        $this->insert('{{%user}}', [
//            'username' => 'god',
//            'id' => 1,
//            'balance' => 9999,
//        ]);        
    }
 
    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
