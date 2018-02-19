<?php

namespace app\controllers;

use app\components\FrontController;
use app\models\Resume;
use Yii;

class ResumeController extends FrontController
{
    public function actionCreate()
    {
        $this->setTitle('');
        if (Yii::$app->request->isAjax)
        {
            $model = new Resume();
            $model->user_id = Yii::$app->user->getIdentity()->getId();
            if ($model->load(Yii::$app->request->post()) && $model->validate())
            {
                $model->created_at = time();
                $model->save(false);

                $model->setSkills();

                Yii::$app->getSession()->setFlash('resume_create', [
                    'title' => 'Резюме добавлено!'
                ]);

                return $this->asJson([
                    'success' => true,
                ]);
            }

            return $this->asJson([
                'success' => false,
                'error' => $model->errors,
                'toaster' => Yii::t('app', 'ERROR')
            ]);
        }

        return $this->render('create');
    }

}
