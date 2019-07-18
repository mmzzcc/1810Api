<?php

namespace App\Admin\Controllers;

use App\Model\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UserController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User);

        $grid->u_id('U id');
        $grid->user_name('User name');
        $grid->user_pwd('User pwd');
        $grid->user_tel('User tel');
        $grid->user_email('User email');
        $grid->add_time('Add time');
        $grid->appid('Appid');
        $grid->appsecret('Appsecret');
        $grid->user_pic('User pic');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->u_id('U id');
        $show->user_name('User name');
        $show->user_pwd('User pwd');
        $show->user_tel('User tel');
        $show->user_email('User email');
        $show->add_time('Add time');
        $show->appid('Appid');
        $show->appsecret('Appsecret');
        $show->user_pic('User pic');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new User);

        $form->text('user_name', 'User name');
        $form->password('user_pwd', 'User pwd');
        $form->text('user_tel', 'User tel');
        $form->email('user_email', 'User email');
        $form->file('user_pic', 'User pic');

        return $form;
    }
}
