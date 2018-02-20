<?php

namespace app\models;

use app\components\PasswordBehavior;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $avatar
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property int $birth
 * @property string $biography
 * @property int $status_id
 * @property int $social_id
 * @property int $created_at
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $password;
    public $password_confirm;
    public $avatar_image;

    const STATUS_WAIT = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_BLOCKED = 3;

    const SCENARIO_SIGNIN = 'login';
    const SCENARIO_SIGNUP = 'register';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_LOAD_AVATAR = 'load_avatar';

    const GENDER_MALE = 'men';
    const GENDER_FEMALE = 'women';

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'gender' => 'Gender',
            'avatar' => 'Avatar',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'birth' => 'Birth',
            'biography' => 'Biography',
            'status_id' => 'Status ID',
            'social_id' => 'Social ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Список поведений
     *
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'password' => [
                'class' => PasswordBehavior::className(),
                'attribute' => 'password',
            ]
        ]);
    }

    /**
     * Доступные сценарии
     *
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_SIGNUP] = ['first_name', 'last_name', 'email', 'password', 'password_confirm',
            'gender', 'recaptcha', 'social_id'];

        $scenarios[self::SCENARIO_SIGNIN] = ['email', 'password'];
        $scenarios[self::SCENARIO_UPDATE] = ['first_name', 'last_name', 'password', 'password_confirm', 'birth',
            'biography', 'gender', 'avatar'];
        $scenarios[self::SCENARIO_LOAD_AVATAR] = ['avatar_image'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $aRules = [
            self::SCENARIO_SIGNUP => [
                [['email', 'password', 'first_name', 'last_name'], 'trim'],
                [['email', 'password', 'first_name', 'last_name', 'gender'], 'required'],
                [['email'], 'unique'],
                ['gender', 'checkGender'],
                ['password', 'string', 'min' => 8],
                ['password', 'compare', 'compareAttribute' => 'password_confirm', 'message' => 'Пароли не совпадают'],
            ],
            self::SCENARIO_SIGNIN => [
                [['email', 'password'], 'trim'],
                [['email', 'password'], 'required'],
                [['email'], 'email'],
                [['email'], 'checkStatus'],
            ],
            self::SCENARIO_UPDATE => [
                [['password', 'password_confirm', 'first_name', 'last_name'], 'trim'],
                [['first_name', 'last_name', 'gender'], 'required'],
                ['password', 'string', 'min' => 8],
                ['password', 'compare', 'compareAttribute' => 'password_confirm', 'message' => 'Пароли не совпадают'],
                [['password', 'password_confirm'], 'oneEmpty'],
                ['gender', 'checkGender'],
                ['birth', 'setDate'],
            ],
            self::SCENARIO_LOAD_AVATAR => [
                [['avatar_image'], 'required'],
                [['avatar_image'], 'file', 'skipOnEmpty' => false, 'extensions' => 'jpg, gif, png', 'maxFiles' => 1],
                [['avatar_image'], 'file', 'maxSize' => (500 * 1024), 'tooBig' => 'Размер не должен превышать 500 кб'],
            ],
            self::SCENARIO_DEFAULT => [
                [['email'], 'email'],
                [['birth', 'status_id', 'social_id', 'created_at'], 'default', 'value' => null],
                [['birth', 'status_id', 'social_id', 'created_at'], 'integer'],
                [['first_name', 'last_name', 'email', 'password_hash', 'password_reset_token'], 'string', 'max' => 255],
                [['gender'], 'string', 'max' => 10],
                [['biography'], 'string', 'max' => 1024],
                [['password', 'password_confirm'], 'match', 'pattern' => '/^[A-za-z0-9_-]+$/ui',
                    'message' => "В пароле допустимы только латинские символы и цифры"],

                [['first_name', 'last_name', 'biography'], 'filter', 'filter' => function($value) {
                    return trim(htmlentities(strip_tags($value), ENT_QUOTES, 'UTF-8'));
                }],
            ]
        ];

        return ArrayHelper::merge( $aRules[$this->getScenario()], $aRules[self::SCENARIO_DEFAULT] );
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return null;
    }

    /**
     * Валидация пароля
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Правило проверки статуса пользователя
     *
     * @param $attribute
     * @param $params
     */
    public function checkStatus($attribute, $params)
    {
        if (!$this->hasErrors())
        {
            $user = static::find()->where(['email' => $this->email])->one();

            if (!$user || ($user->status_id == User::STATUS_ACTIVE && !$user->validatePassword($this->password))) {
                $this->addError($attribute, 'User no active!');
            } elseif ($user->status_id == User::STATUS_WAIT) {
                $this->addError($attribute, 'User not confirmed!');
            } elseif ($user->status_id == User::STATUS_BLOCKED) {
                $this->addError($attribute, 'User blocked!');
            }
        }
    }

    /**
     * Проверка правильности выбора пола
     *
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function checkGender($attribute, $params)
    {
        if (!in_array($this->gender, static::getGenderList())) {
            $this->addError($attribute, 'Параметр "пол" указан неверно');
            return false;
        }
        return true;
    }

    /**
     * Метод проверяет заполнение поля пароля и подтверждения
     *
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function oneEmpty($attribute, $params)
    {
        if ($this->password && !$this->password_confirm || !$this->password && $this->password_confirm) {
            $this->addError($attribute, "Необходимо заполнить оба поля для смены пароля");
            return false;
        }
        return true;
    }

    /**
     * Преобразование даты
     *
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function setDate($attribute, $params)
    {
        $this->birth = strtotime($this->birth);
    }

    /**
     * @return array
     */
    public static function getGenderList()
    {
        return [self::GENDER_MALE, self::GENDER_FEMALE];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * Авторизуем пользвателя
     *
     * @return bool
     */
    public function login()
    {
        return Yii::$app->user->login($this, 3600*24*180);
    }

    /**
     * Возвращает сконкатенированные имя и фамилию
     *
     * @return string
     */
    public function getFull_name()
    {
        return $this->first_name . " " . $this->last_name;
    }

    /**
     * Генерирует токен для подтверждения и сброса пароля
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Формирование ссылки для подтверждения адреса email
     *
     * @return string
     */
    public function getEmail_confirm_link()
    {
        return Url::to(["site/email-confirm", "h" => $this->password_reset_token], true);
    }

    /**
     * Поиск по токену
     *
     * @param $hash
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findByHash($hash)
    {
        return static::find()->where(['password_reset_token' => $hash])->one();
    }

    /**
     * Генерация хеша пароля
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Авторизация по email
     *
     * @return bool
     */
    public function loginByEmail()
    {
        $user = static::find()->where(['email' => $this->email])->one();
        if (!$user) {
            return false;
        }
        return $user->login();
    }

    /**
     * Маппим данные из ВК
     *
     * @param $data
     * @return array
     */
    public static function formatDataVk($data)
    {
        $formatData = [];

        if(isset($data['email']) && $data['email']) {
            $formatData['email'] = $data['email'];
        }

        if (isset($data['id']) && $data['id']) {
            $formatData['social_id'] = $data['id'];
        }

        if (isset($data['first_name'])) {
            $formatData['first_name'] = $data['first_name'];
        }

        if (isset($data['last_name'])) {
            $formatData['last_name'] = $data['last_name'];
        }

        if (isset($data['sex']) && $data['sex']) {
            $formatData['gender'] = (2 == $data['sex'] ? User::GENDER_MALE : User::GENDER_FEMALE);
        }

        return $formatData;
    }

    /**
     * Возвращает аватар или заглушку
     *
     * @return string
     */
    public function getAvatar_or_stub()
    {
        return $this->avatar ? $this->avatar : '/images/avatars/' . $this->gender . '.jpg';
    }

    /**
     * @return bool
     */
    public function isMen()
    {
        return $this->gender == static::GENDER_MALE;
    }

    /**
     * @return bool
     */
    public function isWomen()
    {
        return $this->gender == static::GENDER_FEMALE;
    }

    /**
     * @return false|string
     */
    public function getBirthday_formatted()
    {
        return $this->birth ? date("m-d-Y", $this->birth) : "";
    }

    /**
     * Полный физический путь к папке для загрузки
     *
     * @return bool|string
     */
    public static function getFullPath()
    {
        return Yii::getAlias("@app/public_html/uploads/avatar/");
    }

    /**
     * Путь к аватарке через веб
     *
     * @return bool|string
     */
    public static function getWebPath()
    {
        return Yii::getAlias("@web/uploads/avatar/");
    }

    /**
     * Генерация случайного имени
     *
     * @param $file
     * @return string
     */
    public static function generateImageName($file)
    {
        return uniqid() . "." . $file->extension;
    }

    /**
     * Проверяет или пытается создать директорию для загрузки
     *
     * @return bool
     */
    public function checkPath()
    {
        $path = $this->getFullPath();
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Сохранение картинки
     *
     * @return bool
     */
    public function saveImage()
    {
        $this->avatar_image = UploadedFile::getInstance($this,'avatar_image');
        if (!$this->avatar_image || !$this->checkPath() || !$this->validate()) {
            $this->addError("avatar_image", "Ошибка загрузки файла");
            return false;
        }

        $newImageName = static::generateImageName($this->avatar_image);
        if ($this->avatar_image->saveAs($this->getFullPath() . $newImageName, true))
        {
            $this->avatar = static::getWebPath() . $newImageName;
            return true;
        }

        $this->addError("avatar_image", "Ошибка загрузки файла");
        return false;
    }

    public function getResume()
    {
        return $this->hasMany(Resume::className(), ["user_id" => "id"]);
    }
}
