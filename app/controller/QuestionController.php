<?php
/**
 * 说明:反馈
 *
 * User: 胡熔
 * Date: 2018/11/12
 * Time: 18:14
 */

namespace app\controller;
class QuestionController extends CommonController
{

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    /**
     * 反馈列表
     */
    public function index()
    {
        $day = !empty($_POST['day']) ? trim($_POST['day']) : '';
        $status = !empty($_POST['status']) ? trim($_POST['status']) : '';
        $page = !empty($_POST['page']) ? trim($_POST['page']) : 1;

    }

    /**
     * 新建反馈
     */
    public function add()
    {
        $content = !empty($_POST['content']) ? trim($_POST['content']) : '';
    }

}