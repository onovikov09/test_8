<?php

namespace app\controllers;

use app\components\FrontController;
use app\components\Helper;
use app\models\Resume;
use app\models\User;
use Yii;
use yii\authclient\BaseOAuth;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;

class SiteController extends FrontController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signin', 'signup'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['signin', 'signup'],
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'signin' => ['post'],
                    'signup' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'actionOnAuthSuccess'],
                'cancelUrl' => Url::to(['/']),
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->setTitle('Список резюме');

        return $this->render('index', [
            'list' => Resume::find()->where(['is_active' => Resume::STATUS_ACTIVE])->with(['user'])
                ->orderBy("sort ASC")->all(),
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionSignin()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Url::to(['/']));
        }

        $model = new User(['scenario' => User::SCENARIO_SIGNIN]);
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            Helper::clearSessionStorage('social');

            return $this->asJson([
                'success' => $model->loginByEmail(),
                'link' => Url::to(['/'])
            ]);
        }

        return $this->asJson([
            'success' => false,
            'error' => $model->errors,
            'toaster' => 'Ошибка авторизации!'
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Регистрация через Вконтакте
     *
     * @param $client
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionOnAuthSuccess($client)
    {
        if ($client instanceof BaseOAuth)
        {
            $attributes = $client->getUserAttributes();
            if (empty($attributes)) {
                throw new NotFoundHttpException();
            }

            if ($user = User::findOne(['social_id' => $attributes['id']]))
            {
                if ($user->status_id == User::STATUS_WAIT)
                {
                    Yii::$app->getSession()->setFlash('auth_social', [
                        'title' => 'Ошибка входа!',
                        'text' => 'Адрес электронной почты не подтвержден'
                    ]);

                } elseif ($user->status_id == User::STATUS_BLOCKED)
                {
                    Yii::$app->getSession()->setFlash('auth_social', [
                        'title' => 'Ошибка входа!',
                        'text' => 'Пользователь заблокирован'
                    ]);

                } elseif ($user->status_id == User::STATUS_ACTIVE) {
                    $user->login();
                }

                return $this->redirect(Url::to(['/']));
            }

            $session = Yii::$app->session;
            if (!$session->isActive)
                $session->open();

            $session->set('social', User::formatDataVk($attributes));
            return $this->redirect(Url::to(['/', '#' => 'signup']));
        }

        throw new NotFoundHttpException();
    }

    /**
     * Регистрация
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSignup()
    {
        if (!Yii::$app->request->isAjax) {
            throw new NotFoundHttpException();
        }

        $model = new User(['scenario' => User::SCENARIO_SIGNUP]);
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->generatePasswordResetToken();
            $model->created_at = time();
            $model->save(false);

            Helper::clearSessionStorage('social');

            Yii::$app->getSession()->setFlash('auth_social', [
                'title' => 'Для подтвержения адреса электронной почты перейдите по ссылке из письма',
            ]);

            return $this->asJson([
                'success' => Helper::sendMail('confirm_email', 'Подтверждение адреса электронной почты', $model)
            ]);
        }

        return $this->asJson([
            'success' => false,
            'error' => $model->errors,
            'toaster' => 'Ошибка регистрации'
        ]);
    }

    /**
     * Подтверждение email
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionEmailConfirm()
    {
        $code = Yii::$app->request->get('h', false);
        if (!$code || empty($user = User::findByHash($code))) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($user->status_id == User::STATUS_WAIT)
        {
            $user->status_id = User::STATUS_ACTIVE;
            $user->password_reset_token = null;
            $user->save(false);

            Yii::$app->getSession()->setFlash('email_confirm', [
                'title' => 'Адрес электронной почты подтвержден!',
            ]);

            $user->login();
            Helper::sendMail('email_confirmed',  'Адрес электронной почты подтвержден', $user);

        } else {
            Yii::$app->getSession()->setFlash('email_confirm', [
                'title' => 'Адрес электронной почты уже подтвержден'
            ]);
        }

        return $this->redirect(Url::to(['/']));
    }

    /**
     * Карта сайта
     *
     */
    public function actionSitemap()
    {
        $this->layout = false;
        if (!$xmlSitemap = Yii::$app->cache->get('sitemap')) {
            $xmlSitemap = $this->renderPartial('sitemap', [
                'items' => Resume::find()->where(['is_active' => Resume::STATUS_ACTIVE])->with(['user'])->all()
            ]);

            //@TODO тут нужно поставить больше, стоит 1 для примера
            Yii::$app->cache->set('sitemap', $xmlSitemap, 1);
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        header("Content-type: text/xml");
        echo $xmlSitemap; exit;
    }
}
