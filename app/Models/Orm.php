<?php
namespace App\Models;

abstract class Orm extends \Illuminate\Database\Eloquent\Model
{
    /**
     * 设置主键字段
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 设置创建时间字段
     */
    const CREATED_AT = 'create_time';

    /**
     * 设置更新时间字段
     */
    const UPDATED_AT = 'update_time';

    const DELETED_AT = null;

    #软删除状态值 0:正常;1:删除
    const INVALID_STATUS = 1;

    /**
     * 设置状态字段
     */
    const STATUS = 'is_delete';

    public function __construct(array $attributes = [])
    {
        \App::make('database');
        parent::__construct($attributes);
    }

    /**
     * 列表查询
     * @param array $aCondition 条件 (非必需参数)
     *        可以使用的条件:'>','<','>=','<=','<>','!=','like',where,in，notin，between，notbetween，or，orderby
     *        使用方法见下面的例子；
          'aCondition' => [
               'sRecommendMobile' => '15262281953', //正常的where条件 sRecommendMobile = 15262281953；
               '<'                => ['iLastLoginTime' => 14514717626], //代表iLastLoginTime<14514717626;
               '<>'               => ['iUserID' => 47613, 'iLoginTimes' => 5],//代表iUserID<>47613,iLoginTimes<>5；
               'like'             => ['sName' => '%aaa'],//代表sName like %aaa；
               'between'          => ['iPayCenterBid' => [1, 10]],//代表 iPayCenterBid between 1 and 10；
               'notin'            => ['iCompanyID' => [1, 2]],//代表 not in (1,2)；
               'or'               => ['sRecommendMobile' => '15262281953'],//代表 or sRecommendMobile=15262281953；
               'orderby'          => ['iLastLoginTime' => 'desc'] //代表 order by iLastLoginTime desc；
          ]
     * @param int $iPage    当前页
     * @param int $iPerPage 每页多少条
     * @param array $order  主要按哪个字段排序:如['iAutoID'=>'desc']
     * @param int $iLastID  当前页最后一条数据的ID
     * @return array
     */
    public static function spList($aCondition = [], $iPage = 1, $iPerPage = 10, $order = [], $iLastID = 0)
    {
        //返回结果格式
        $aResult   = [
            'total'    => 0,
            'page'     => $iPage,
            'page_size' => $iPerPage,
            'last_id'  => $iLastID,
            'list'     => []
        ];
        //参数取值限制
        $iPage    = max(intval($iPage), 1);
        $iPerPage = min(max(intval($iPerPage), 1), 1000);
        $iLastID  = max(intval($iLastID), 0);
        //根据条件筛选获取结果
        $oModel = new static;
        //获取主键
        $primaryKey = isset($oModel->primaryKey) ? $oModel->primaryKey : 'id';
        $columns    = $oModel->columns;
        $oModel     = $oModel->getCondition($oModel, $aCondition);
        //获取总数
        $aResult['total'] = $oModel->count();
        if ($aResult['total'] > 0) {
            //查询指定字段
            $field = isset($aCondition['field']) && is_array($aCondition['field']) ? $aCondition['field'] : ['*'];
            $field = empty($field) ? ['*'] : $field;
            //获取排序条件
            $order = !empty($order) && is_array($order) ? $order : [$primaryKey => 'desc'];
            foreach ($order as $k => $v) {
                if (in_array(strtolower($v), ['asc', 'desc'])) {
                    $oModel = $oModel->orderBy($k, $v);
                }
            }
            //大数据分页
            if ($primaryKey && !empty($iLastID)) {
                $fh     = !empty($order[$primaryKey]) && ($order[$primaryKey] == 'asc') ? '>' : '<';
                $oModel = $oModel->where($primaryKey, $fh, $iLastID);
                $res    = $oModel->limit($iPerPage)->get($field)->toArray();
            } else { //常规分页
                $offset = ($iPage - 1) * $iPerPage;
                $res    = $oModel->skip($offset)->take($iPerPage)->get($field)->toArray();
            }
            $aResult['list']  = $res;
        }
        return $aResult;
    }

    /**
     * 处理条件参数
     * @param object $oModel   数据模型Model
     * @param array  $aCondition   筛选条件
     * 'aCondition'=>[
    'iType'   => 1,
    '<'       => ['iLastLoginTime' => 14514717626],
    '<>'      => ['iUserID' => 47613, 'iLoginTimes' => 5],
    'between' => ['iPayCenterBid' => [1, 10]],
    'notin'   => ['iCompanyID' => [1, 2]],
    'or'      => ['sRecommendMobile' => '15262281953'],
    'orderby' => ['iLastLoginTime' => 'desc']
    ]
     * 可以使用的条件：where,in，notin，between，notbetween，or，orderby；后期可扩充
     * @return mixed
     */
    public function getCondition($oModel, $aCondition)
    {
        //$newModel = new static;
        //where条件
        $where = ['>', '<', '>=', '<=', '<>', '!=', 'like','&','regexp','rawSql','with'];
        $other = [
            'in'         => 'whereIn',
            'notin'      => 'whereNotIn',
            'between'    => 'whereBetween',
            'notbetween' => 'whereNotBetween',
            'or'         => 'orWhere',
            'ornew'      => 'orWhere',//仅仅很简单处理OR的逻辑，不冗余业务逻辑
            'orderby'    => 'orderBy'
        ];
        foreach ($aCondition as $key => $val) {
            //键值对(eq情况)直接使用where条件，例如'sSketch'=>115551
            if (!is_array($val)) {
                if ($key === 'rawSql')
                {
                    $oModel = $oModel->whereRaw($val);
                } else {
                    $oModel = $oModel->where($key, $val);
                }
            } else {
                //区间单值查询，例如'<' => ['iLastLoginTime' => 14514717626]
                if (in_array($key, $where))
                {
                    if ('with' === $key && is_array($val)) {
                        $oModel = $oModel->with($val);
                    } else {
                        foreach ($val as $k => $v) //循环where条件
                        {
                            $oModel = $oModel->where($k, $key, $v);
                        }
                    }
                }
                //区间数组查询，例如'between' => ['iPayCenterBid' => [1, 10]]
                elseif (in_array(strtolower($key), array_keys($other))) {
                    //循环where条件
                    if($key == 'or') {
                        $oModel = self::handleOr($oModel, $val);
                    }elseif($key == 'ornew'){
                        $oModel = self::handleOrNew($oModel, $val);
                    }else{
                        foreach ($val as $k => $v) {
                            $oModel = $oModel->{$other[strtolower($key)]}($k, $v);
                        }
                    }
                }
            }
        }
        return $oModel;
    }

    /**
     * desc：配合getCondition方法处理Or规则
     * @param object $oModel 数据模型
     * @param array $val or规则数组
     * @return obj
     */
    protected static function handleOr($oModel, $val){
        $oModel = $oModel->where(function ($query) use($val){
            $j = 0;
            foreach($val as $field=>$value) {
                $j++;
                if ($j==1) {
                    $query = $query->where($field, $value);
                } else {
                    $query = $query->orWhere($field, $value);
                }
            }
        });
        return $oModel;
    }
    private static function handleOrNew($oModel, $val){
        if(empty($val) && !is_array($val)){
            return $oModel;
        }
        foreach($val as $_k =>$_v){
            if(is_array($_v)){
                foreach($_v as $value){
                    $oModel = $oModel->orWhere($_k, $value);
                }
            }else{
                $oModel = $oModel->orWhere($_k, $_v);
            }
        }
        return $oModel;
    }
    /**
     * 按主键获取数据
     * @param $id   主键id
     * @return array
     */
    public static function spGetByID($id)
    {
        return parent::find($id);
    }
    
    /**
     * 根据条件获取单条数据
     *
     * @param  array  $aCondition
     * @param  string[]  $columns
     *
     * @return array | mix
     */
    public static function spGetOne($aCondition = [], $columns = ['*'])
    {
        $oModel = new static();
        $oModel = $oModel->getCondition($oModel, $aCondition);
    
        if (!empty($columns)) {
            $oModel = $oModel->select($columns);
        }
        return $oModel->first();
    }

    /**
     * 根据ID获取单条数据
     * @param $id
     * @param $columns
     * @return array
     */
    public static function spFindOne($id,$columns = ['*']){
        $info = parent::find($id,$columns);
        return is_null($info)?[]:$info->toArray();
    }

    /**
     * 根据条件获取所有数据
     * @param $aCondition
     *
     * @param $aColumn
     * @return array
     */
    public static function spGetAll($aCondition = [], $aColumn = [])
    {
        $oModel = new static();
        $oModel = $oModel->getCondition($oModel, $aCondition);
        //如果设置要查询的字段 只查询要设置的字段，
        if(!empty($aColumn)) {
            $oModel = $oModel->select($aColumn);
        }
        return $oModel->get();
    }

    /**
     * 根据条件获取条数
     * @param $aCondition
     * @return int
     */
    public static function spGetCount($aCondition = [])
    {
        $oModel = new static();
        return $oModel->getCondition($oModel, $aCondition)->count();
    }

    /**
     * 按条件更新
     * @param array $aCondition 筛选条件
     * @param array $aData  更新数据
     * @return bool
     */
    public static function spUpdate($aCondition = [], $aData = [])
    {
        $oModel = new static();
        if(!is_array($aCondition) && empty($aCondition)){
            return false;
        }
        return $oModel->getCondition($oModel, $aCondition)->update($aData);
    }

    /**
     * 按主键更新
     * @param $id   主键
     * @param array $aData  更新数据
     * @return mixed
     */
    public static function spUpdateByID($id, $aData = [])
    {
        return self::where((new static())->primaryKey, $id)->update($aData);
    }

    /**
     * 快速插入记录
     * @param array $aData 需要添加的数据
     * @return bool|static
     */
    public static function spCreate($aData = [])
    {
        if(!$aData){
            return false;
        }
        return self::create($aData);
    }

    /**
     * 插入记录
     * @param array $aData  需要添加的数据
     * @return bool
     */
    public static function spAdd($aData = [])
    {
        $obj = new static;
        foreach ($aData as $key => $val) {
            $obj->$key = $val;
        }
        return $obj->save();
    }

    /**
     * 插入记录 返回insertid
     * @param array $aData  需要添加的数据
     * @return array
     */
    public static function spStore($aData = [])
    {
        $obj = new static();
        foreach ($aData as $key => $val) {
            $obj->$key = $val;
        }
        $obj->save();
        return $obj;
    }

    /**
     * 软删除数据
     * @param array $aCondition 筛选条件
     * @return mixed
     */
    public static function spDelete($aCondition = [])
    {
        $oModel   = new static();
        return $oModel->getCondition($oModel, $aCondition)->delete();
    }

    /**
     * 获取当前时间
     * @return integer
     */
    public function freshTimestamp()
    {
        return time();
    }

    /**
     * 转换日期时间
     * @param  $mValue 日期时间
     * @return integer
     */
    public function fromDateTime($mValue)
    {
        return $mValue;
    }

    /**
     * 使用时间戳, 不自动格式化时间
     * @return array
     */
    public function getDates()
    {
        return [];
    }

    /**
     * 获取表名
     *
     * @return string
     */
    public static function getTableName()
    {
        return (new static())->getTable();
    }

    /**
     * 处理条件参数，所有条件都带上表名
     * @param array $aCondition   筛选条件
     * 'aCondition'=>[
     * 'iType'   => 1,
     * '<'       => ['iLastLoginTime' => 14514717626],
     * '<>'      => ['iUserID' => 47613, 'iLoginTimes' => 5],
     * 'between' => ['iPayCenterBid' => [1, 10]],
     * 'notin'   => ['iCompanyID' => [1, 2]],
     * 'or'      => ['sRecommendMobile' => '15262281953'],
     * 'orderby' => ['iLastLoginTime' => 'desc']
     * ]
     * 可以使用的条件：where,in，notin，between，notbetween，or，orderby；后期可扩充
     * @return object
     */
    public function getSpecialCondition($aCondition)
    {
        if (empty($aCondition)) {
            return $this;
        }
        $oModel = $this;
        $tabName = $oModel->getTable();

        //where条件
        $where = ['>', '<', '>=', '<=', '<>', '!=', 'like', '&'];
        $other = [
            'in' => 'whereIn',
            'notin' => 'whereNotIn',
            'between' => 'whereBetween',
            'notbetween' => 'whereNotBetween',
            'or' => 'orWhere',
            'orderby' => 'orderBy',
            'isnotnull'=>'whereNotNull',
        ];
        foreach ($aCondition as $key => $val) {
            //键值对(eq情况)直接使用where条件，例如'sSketch'=>115551
            if (!is_array($val)) {
                $oModel = $oModel->where($tabName.'.'.$key, $val);
            } else {
                //区间单值查询，例如'<' => ['iLastLoginTime' => 14514717626]
                if (in_array($key, $where)) {
                    //循环where条件
                    foreach ($val as $k => $v) {
                        $oModel = $oModel->where($tabName.'.'.$k, $key, $v);
                    }
                } //区间数组查询，例如'between' => ['iPayCenterBid' => [1, 10]]
                elseif (in_array(strtolower($key), array_keys($other))) {
                    //循环where条件
                    if ($key == 'or') {
                        $oModel = $oModel->specialHandleOr($val,$tabName);
                    } else {
                        foreach ($val as $k => $v) {
                            $oModel = $oModel->{$other[strtolower($key)]}($tabName.'.'.$k, $v);
                        }
                    }
                }
            }
        }

        return $oModel;
    }

    /**
     * desc：配合getCondition方法处理Or规则
     * @param array $val or规则数组
     * @return obj
     */
    private function specialHandleOr($val,$tabName)
    {
        return $this->where(function ($query) use ($val,$tabName) {
            $j = 0;
            foreach ($val as $field => $value) {
                $j++;
                if ($j == 1) {
                    $query = $query->where($tabName.'.'.$field, $value);
                } else {
                    $query = $query->orWhere($tabName.'.'.$field, $value);
                }
            }
        });
    }

}
