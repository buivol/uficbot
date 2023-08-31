<?php

use ActiveRecord\Model;
class User extends Model
{
    public $id;
	public $nickname;
	public $first_name;
	public $second_name;
	public $last_name;
	public $phone;
	public $created_at;
	public $step;
	public $last_message;
	public $is_admin;


}