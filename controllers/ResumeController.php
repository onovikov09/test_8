<?php

namespace app\controllers;

use app\components\FrontController;
use app\models\Resume;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

class ResumeController extends FrontController
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
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'edit', 'delete', 'view'],
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Добавление нового резюме
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $this->setTitle('Новое резюме');
        if (Yii::$app->request->isAjax)
        {
            $params = Yii::$app->request->post();

            if (isset($params["Resume"]['id']) && $params["Resume"]['id']) {
                return $this->actionEdit($params["Resume"]['id']);
            }

            $model = new Resume();
            $model->user_id = Yii::$app->user->getIdentity()->getId();

            if ($model->load($params) && $model->validate())
            {
                $model->created_at = time();
                $model->update_at = time();
                $model->save(false);

                $model->setSkills();

                Yii::$app->getSession()->setFlash('resume_create', [
                    'title' => 'Резюме добавлено!'
                ]);

                return $this->redirect(Url::to(["resume/view", "id"=>$model->id]))->send();
            }

            return $this->asJson([
                'success' => false,
                'error' => $model->errors,
                'toaster' => 'Ошибка добавления резюме!'
            ]);
        }

        return $this->render('create', ['resume' => new Resume()]);
    }

    /**
     * Редактирование резюме
     *
     * @param $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionEdit($id)
    {
        $resume = Resume::find()->where(["id" => $id])->andWhere(["is_active"=>Resume::STATUS_ACTIVE])->one();
        if (!$resume || $resume->isForeign()){
            return $this->redirect(Url::to(["/"]));
        }

        if (Yii::$app->request->isAjax)
        {
            if ($resume->load(Yii::$app->request->post()) && $resume->validate())
            {
                $resume->update_at = time();
                $resume->save(false);

                $resume->setSkills();

                Yii::$app->getSession()->setFlash('resume_create', [
                    'title' => 'Резюме обновлено!'
                ]);

                return $this->asJson(['success' => true]);
            }

            return $this->asJson([
                'success' => false,
                'error' => $resume->errors,
                'toaster' => 'Ошибка обновления резюме!'
            ]);
        }

        $this->setTitle('Редактирование резюме');
        return $this->render('create', ['resume' => $resume]);
    }

    /**
     * Просмотр резюме
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $this->setTitle('Просмотр резюме');
        $resume = Resume::find()->where(["id" => $id])->andWhere(["is_active"=>Resume::STATUS_ACTIVE])->one();
        if (!$resume){
            return $this->redirect(Url::to(["/"]));
        }

        $this->setTitle('Просмотр резюме');
        return $this->render('view', ['resume' => $resume]);
    }

    /**
     * Удаление резюме
     *
     * @param $id
     * @return \yii\web\Response
     */
    public function actionDelete($id)
    {
        $resume = Resume::find()->where(["id" => $id])->andWhere(["is_active"=>Resume::STATUS_ACTIVE])->one();
        if ($resume && !$resume->isForeign()) {
            $resume->delete();
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}
