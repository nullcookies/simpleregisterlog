<?php


class AuthController extends nomvcBaseControllerTwo {

    protected function init() {
        parent::init();
    }
    
    public function run() {
        $request = $this->context->getRequest();
        $generator = new OutputGenerator($this->context, $this);
        $form = new LoginForm($this->context, array('method' => 'post', 'action' => $this->baseUrl.'/login'));
        $form->init();
        
        $panelClass = 'panel panel-default';
        if ($request->isPost()) {
            if ($form->validate($request->getParameter('login'))) {
                $user = $this->context->getUser();
            
                if ($user->signin($form->getValue('login'), $form->getValue('password'))) {
                    $this->redirect($this->baseUrl);
                }
                else{
                    $form->setErrorMessage('Указаны некорректные логин/пароль');
                    $panelClass = 'panel panel-danger';
                }
            }
        }
        return $generator->prepare('auth', array(
            'content' => $form->render('login'),
            'panelClass' => $panelClass,
        ))->run();
    }
    
    protected function makeUrl() {
        return '';
    }

}
