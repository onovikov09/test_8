<?php

use yii\db\Migration;

/**
 * Class m180219_122150_insert_data_skills
 */
class m180219_122150_insert_data_skills extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('skill', [
            'title' => 'PHP',
        ]);

        $this->insert('skill', [
            'title' => 'Mysql',
        ]);

        $this->insert('skill', [
            'title' => 'JavaScript',
        ]);


        $this->insert('user', [
            'first_name' => 'Christopher',
            'last_name' => 'Di',
            'email' => 'email@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/2.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Marrie',
            'last_name' => 'Doi',
            'email' => 'email1@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/1.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Heather',
            'last_name' => 'Hoi',
            'email' => 'email2@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/3.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'email3@gmail.com',
            'gender' => 'men',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/4.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Peter',
            'last_name' => 'John',
            'email' => 'email4@gmail.com',
            'gender' => 'men',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/5.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Cherry',
            'last_name' => 'John',
            'email' => 'email5@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/6.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Frank',
            'last_name' => 'Martin',
            'email' => 'email6@gmail.com',
            'gender' => 'men',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/7.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Christopher',
            'last_name' => 'Di',
            'email' => 'email7@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/8.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Heather',
            'last_name' => 'Heat',
            'email' => 'email8@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/9.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Nancy',
            'last_name' => 'Doe',
            'email' => 'email9@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/10.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Stella',
            'last_name' => 'John',
            'email' => 'email10@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/11.jpg'
        ]);

        $this->insert('user', [
            'first_name' => 'Cherry',
            'last_name' => 'John',
            'email' => 'email11@gmail.com',
            'gender' => 'women',
            'password_hash' => '123123',
            'avatar' => '/images/avatars/12.jpg'
        ]);



        $this->insert('resume', [
            'user_id' => 1,
            'title' => 'Web Developer',
            'description' => 'Пишу и читаю со словарем',
        ]);

        $this->insert('resume', [
            'user_id' => 2,
            'title' => 'Designer',
            'description' => 'Люблю рисовать',
        ]);

        $this->insert('resume', [
            'user_id' => 3,
            'title' => 'Developer',
            'description' => 'Люблю летать',
        ]);

        $this->insert('resume', [
            'user_id' => 4,
            'title' => 'Co-founder/ Marketing',
            'description' => 'Шью и вяжу',
        ]);

        $this->insert('resume', [
            'user_id' => 5,
            'title' => 'Co-founder/ Projects',
            'description' => 'Не люблю летать',
        ]);

        $this->insert('resume', [
            'user_id' => 6,
            'title' => 'Fullstack Developer',
            'description' => 'Люблю кодить',
        ]);

        $this->insert('resume', [
            'user_id' => 7,
            'title' => 'Co-founder/ Operations',
            'description' => 'Работаю за еду',
        ]);

        $this->insert('resume', [
            'user_id' => 8,
            'title' => 'Designer',
            'description' => 'Полет мысли',
        ]);

        $this->insert('resume', [
            'user_id' => 9,
            'title' => 'Co-founder/ Projects',
            'description' => 'Не люблю летать',
        ]);

        $this->insert('resume', [
            'user_id' => 10,
            'title' => 'Co-founder/ Projects',
            'description' => 'Не люблю летать',
        ]);

        $this->insert('resume', [
            'user_id' => 11,
            'title' => 'Co-founder/ Projects',
            'description' => 'Не люблю летать',
        ]);

        $this->insert('resume', [
            'user_id' => 12,
            'title' => 'Co-founder/ Projects',
            'description' => 'Не люблю летать',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->truncateTable('skill');
        $this->truncateTable('user');
        $this->truncateTable('resume');
    }
}
