<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\ContactForm;
use app\models\User;
use app\models\UserEditForm;
use app\models\UserFilterForm;
use yii\bootstrap\Modal;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
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
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index',[
            'username' => Yii::$app->user->isGuest ? "" : User::getNameById(Yii::$app->user->id),
        ]);
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRegister()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();

        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->session->setFlash('userRegistered');
            return $this->redirect(['site/login']);
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /*
     * Admin actions
     */
    
    public function actionAdmin()
    {
        // Handle filter refresh
        //
        if(isset($_POST['refresh']))
        {
            Yii::$app->session['filter_name'] = $_POST['name'];
            Yii::$app->session['filter_phone'] = $_POST['phone'];
            Yii::$app->session['filter_email'] = $_POST['email'];
            Yii::$app->session['filter_role'] = $_POST['role'];
        }

        $model = [];
        $model['name'] = isset(Yii::$app->session['filter_name']) ? Yii::$app->session['filter_name'] : "";
        $model['phone'] = isset(Yii::$app->session['filter_phone']) ? Yii::$app->session['filter_phone'] : "";
        $model['email'] = isset(Yii::$app->session['filter_email']) ? Yii::$app->session['filter_email'] : "";
        $model['role'] = isset(Yii::$app->session['filter_role']) ? Yii::$app->session['filter_role'] : 2;

        $userrolesfilter = $userroles = User::getUserTypes();
        array_push($userrolesfilter, "Все");

        return $this->render('admin', [
            'users' => User::getAllUsers(),
            'userroles' => $userroles,
            'userrolesfilter' => $userrolesfilter,
            'filterstate' => $model,
        ]);
    }

    public function actionAdminuser()
    {
        if(isset($_POST['delete'])) 
        {
            User::deleteUser($_POST['id']);
            Yii::$app->session->setFlash('userDeleted', $_POST['id']);

        } else if(isset($_POST['update'])) 
        {
            $user = [];
            $user['id'] = $_POST['id'];
            $user['name'] = $_POST['name'];
            $user['phone'] = $_POST['phone'];
            $user['email'] = $_POST['email'];
            $user['role'] = $_POST['role'];
            User::updateUser($user);
        }

        return $this->redirect(['site/admin', 'id' => $_POST['id']]);
    }

    public function actionEdituser()
    {
        return $this->redirect(['site/adminuser']);
    }

    public function actionNewuser()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->session->setFlash('newUserAdded');
            return $this->redirect(['site/admin']);
        }
        return $this->render('newuser', [
            'model' => $model,
        ]);
    }

    /*
     * Other
     */

    public function actionAbout()
    {
        return $this->render('about');
    }
}
