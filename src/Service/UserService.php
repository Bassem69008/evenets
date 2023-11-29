<?php
namespace  App\Service;

use Symfony\Component\Form\FormFactoryInterface;
class UserService
{
    public function __construct(private FormFactoryInterface $formFactory){

    }
    public function add()
    {

    }

    public function edit()
    {

    }

    function createPassword($length = 8)

    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $string;
    }

}