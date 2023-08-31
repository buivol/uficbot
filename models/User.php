<?php
namespace models;

use Yiisoft\ActiveRecord\ActiveRecord;
use Yiisoft\ActiveRecord\ActiveRecordFactory;
use Yiisoft\Db\Connection\ConnectionInterface;

class User extends ActiveRecord
{
    public $id;
    public $nickname;
    public $first_name;
    public $second_name;
    public $last_name;
    public $phone;
    public $step;
    public $last_message;
    public $is_admin;


    public static function tableName(): string
    {
        return 'user';
    }

}




