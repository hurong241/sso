<?php
/**
 * 说明:
 *
 * User: 胡熔
 * Date: 2018/11/7
 * Time: 12:28
 */

namespace app\controller;

use Predis;

class TokenController extends Controller
{

    public function __construct($controller, $action)
    {
        parent::__construct($controller, $action);
    }

    /**
     * token检查
     */
    public function index()
    {
        $token = !empty($_GET['token']) ? trim($_GET['token']) : '';
        $redirect = !empty($_GET['redirect']) ? urldecode(trim($_GET['redirect'])) : '';
        if (!preg_match('/^[0-9a-zA-Z\+\/]{16,128}$/', $token)) {
            $token = '';
        }
        if ($_SESSION['token'] == $token) {
            if (stripos($redirect, '?') !== false) {
                $redirect .= '&token=' . $token;
            } else {
                $redirect .= '?token=' . $token;
            }
        }
        redirect($redirect);

    }
}