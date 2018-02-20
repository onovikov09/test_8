<?php

use yii\db\Migration;

/**
 * Class m180216_120337_init_tables
 */
class m180216_120337_init_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),
            'gender' => $this->string(10)->notNull(),
            'avatar' => $this->string(255),
            'email' => $this->string(255)->notNull()->unique(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255),
            'birth' => $this->integer(11),
            'biography' => $this->string(1024),
            'status_id' => $this->integer(11)->notNull()->defaultValue(1),
            'social_id' => $this->integer(11),
            'created_at' => $this->integer(11),
        ]);

        $this->createTable('{{%resume}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'title' => $this->string(255)->notNull(),
            'description' => $this->string(1024),
            'sort' => $this->integer(11)->defaultValue(0),
            'is_active' => $this->integer(4)->defaultValue(1),
            'created_at' => $this->integer(11),
            'update_at' => $this->integer(11),
        ]);

        $this->createTable('{{%skill}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'sort' => $this->integer(11)->defaultValue(0),
            'is_active' => $this->integer(4)->defaultValue(1),
        ]);

        $this->createTable('{{%resume2skill}}', [
            'id' => $this->primaryKey(),
            'resume_id' => $this->string(255)->notNull(),
            'skill_id' => $this->integer(11)->notNull(),
            'grade' => $this->smallInteger(4)->notNull()->defaultValue(0),
        ]);

        //$this->createIndex('user_to_resume', 'resume', ['user_id'], false);

        /*$this->addForeignKey(
            'user_fk0',
            'user',
            'id',
            'resume',
            'user_id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'resume_fk0',
            'resume',
            'id',
            'resume2skill',
            'resume_id',
            'RESTRICT',
            'CASCADE'
        );

        $this->addForeignKey(
            'skill_fk0',
            'skill',
            'id',
            'resume2skill',
            'skill_id',
            'RESTRICT',
            'CASCADE'
        );*/
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        /*$this->dropForeignKey("user_fk0", "user");
        $this->dropForeignKey("resume_fk0", "resume");
        $this->dropForeignKey("skill_fk0", "skill");*/

        $this->dropTable('{{%user}}');
        $this->dropTable('{{%resume}}');
        $this->dropTable('{{%skill}}');
        $this->dropTable('{{%resume2skill}}');
    }
}
