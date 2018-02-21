<?php

namespace app\controllers;

use app\components\FrontController;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class ProfileController extends FrontController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['edit', 'image'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Редактирование профиля
     *
     * @return string|\yii\web\Response
     */
    public function actionEdit()
    {
        $this->setTitle('Редактирование профиля');
        if (Yii::$app->request->isAjax)
        {
            $model = Yii::$app->user->getIdentity();
            $model->setScenario(User::SCENARIO_UPDATE);
            if ($model->load(Yii::$app->request->post()) && $model->validate())
            {
                $model->save(false);

                Yii::$app->getSession()->setFlash('profile_edit', [
                    'title' => 'Профиль обновлен!'
                ]);

                return $this->asJson([
                    'success' => true,
                ]);
            }

            return $this->asJson([
                'success' => false,
                'error' => $model->errors,
                'toaster' => 'Ошибка обновления профиля!'
            ]);
        }

        return $this->render("edit");
    }

    /**
     * Загрузка аватара
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionImage()
    {
        if (!Yii::$app->request->isAjax || Yii::$app->user->isGuest) {
            throw new NotFoundHttpException();
        }

        $model = Yii::$app->user->getIdentity();
        $model->setScenario(User::SCENARIO_LOAD_AVATAR);
        if ($model->saveImage()) {
            return $this->asJson(['success' => true, 'new_src' => $model->avatar]);
        }

        return $this->asJson([
            'success' => false,
            'error' => $model->errors,
            'toaster' => 'Ошибка загрузки аватара'
        ]);
    }

}
