<?php
namespace models;

use ActiveRecord\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property double $price
 * @property int $talon_count
 * @property int $zayav_count
 * @property int $print_count
 * @property int $is_delivery
 * @property int $status
 *
 */
class Order extends Model
{

    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_REJECT = 3;


}




