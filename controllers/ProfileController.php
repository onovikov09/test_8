<?php

namespace app\controllers;

use app\components\FrontController;
use app\models\User;
use Yii;

class ProfileController extends FrontController
{
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

}
