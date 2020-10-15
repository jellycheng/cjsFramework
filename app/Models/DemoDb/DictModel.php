<?php namespace App\Models\DemoDb;

use App\Models\Orm;
use App\Enum\DatabaseEnum;
use App\Models\SoftDeletes;

class DictModel extends Orm
{
    use SoftDeletes;
    protected $primaryKey = 'id';
    /**
     * 设置创建时间字段
     */
    const CREATED_AT = 'create_time';

    /**
     * 设置更新时间字段
     */
    const UPDATED_AT = 'update_time';

    /**
     * 设置状态字段
     */
    const STATUS = 'is_delete';

    #软删除状态值 0：有效  1：无效
    const INVALID_STATUS = 1;

    #表名
    protected $table = 'dict';

    protected $connection = DatabaseEnum::DEMO_DB;

}