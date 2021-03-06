<?php

namespace app\controllers;
use app\components\Output;
use app\components\Request;
use app\models\Company;
use app\models\RecruitInfo;
use app\models\DeliveryInfo;
use app\models\CompanyFuli;
use app\models\Street;
use app\models\JobInfo;
use Yii;
use yii\data\Pagination;
/**
 * 企业类
 */
class CompanyController extends \yii\web\Controller
{
    public function init(){ $this->enableCsrfValidation = false; }

    /**
     * 查询企业列表
     */
    public function actionIndex()
    {
        $id=Request::Post("company_id")?Request::Post("company_id"):"a";

        if($id!="a"){
            $sql="SELECT c.*,GROUP_CONCAT(cf.fuli_id) AS fuliid,s.street_name FROM company c LEFT JOIN company_fuli cf ON c.id=cf.company_id  LEFT JOIN street s ON s.id=c.street_id where c.id=".$id." group by c.id";

            $recruit=Yii::$app->db->createCommand($sql)->query(); 
            
            
            
           $result = array();
            if($recruit){
                foreach($recruit as $keys){
                        $result[]=array(
                            'name'=>$keys["name"],
                            'address'=>$keys["address"],
                            'phone_call'=>$keys["phone_call"],
                            'remarks'=>$keys["remarks"],
                            'fuliid'=>$keys["fuliid"]
                        );
                }
                
                return Output::Code(200, $result, "success");
            }else{
                return Output::code(404, '', "没有这个企业");
            }
        }else{
            $query=Company::find();
            $pagination = new Pagination([
                'defaultPageSize' => 5,
                'totalCount' => $query->count(),
            ]);

            $countries = $query
                ->orderBy('id')
                ->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()
                ->all();
               
            $recruit = [
                'countries' => $countries,
                'pagination' => $pagination,
            ];
            if($recruit){
                
                return Output::Code(200, $recruit, "success");
            }else{
                return Output::code(200, "", "success");
            }
        }
        
        
    }

    /**
     * 企业查询所有投递
     */
    public function  actionDelivery(){
        $id=Request::Post("company_id");
        $sql="SELECT d.id,r.url_id,r.username,r.sex,r.id as resumeid,ri.jobName,d.status FROM delivery_info d LEFT JOIN resume r on r.id=d.resume_id LEFT JOIN recruitInfo ri ON ri.id=d.recruit_id WHERE ri.company_id=".$id." GROUP BY d.id";
        $recruit=Yii::$app->db->createCommand($sql)->query(); 
            
            
            
           $result = array();
            if($recruit){
                foreach($recruit as $keys){
                        $result[]=array(
                            'id'=>$keys["id"],
                            'url_id'=>$keys["url_id"],
                            'username'=>$keys["username"],
                            'sex'=>$keys["sex"],
                            'jobName'=>$keys["jobName"],
                            'status'=>$keys["status"]
                        );
                }
                
                return Output::Code(200, $result, "success");
            }else{
                return Output::code(404, '', "没有这个企业");
            }



        return Output::Code(200, $data, "success");
    }
    /*更改投递状态*/
    public function actionDeliverystat(){
        $id=Request::Post("id");
        $stat=Request::Post("stat");
        $model=DeliveryInfo::findOne($id);//id
        $model->status = $stat;
        $model->save(); 
        return Output::code(200,'','更新完成');
    }
    /**
     * 创建企业信息
     */
    public function actionCreateCompany(){
        $model = new Company;
        $data =[            
            "name" => Request::Post("name"),
            "address" => Request::Post("address"),
            "phone_call" => Request::Post("phone_call"),
            "user_id" => Request::Post("user_id"),
            "remarks" => Request::Post("remarks"),
            "street_id"=>Request::Post("streetId")?Request::Post("streetId"):0
        ];
        if($data["name"])
        $model->name = $data["name"];
        if($data["address"])
        $model->address = $data["address"];
        if($data["phone_call"])
        $model->phone_call = $redatas["phone_call"];
        if($data["user_id"])
        $model->user_id = $data["user_id"];
        if($data["remarks"])
        $model->remarks = $data["remarks"];
        if($data["street_id"])
        $model->street_id = (int)$data["street_id"];
        if($model->save() > 0){
            return Output::Code(200, "", "success");
        } else {
            return Output::Code(500, "", "创建失败");
        }
      
    }

     /**
     * 修改企业信息
     */
    public function actionUpdateCompany(){
        
        $data =[        
            "id" => Request::Post("id"),    
            "name" => Request::Post("name"),
            "address" => Request::Post("address"),
            "phone_call" => Request::Post("phone_call"),
            "user_id" => Request::Post("user_id"),
            "remarks" => Request::Post("remarks"),
            "street_id"=>Request::Post("streetId")?Request::Post("streetId"):0
        ];
        $model = Company::findOne($data["id"]);
        if($data["name"])
        $model->name = $data["name"];
        if($data["address"])
        $model->address = $data["address"];
        if($data["phone_call"])
        $model->phone_call = $redatas["phone_call"];
        if($data["user_id"])
        $model->user_id = $data["user_id"];
        if($data["remarks"])
        $model->remarks = $data["remarks"];
        if($data["street_id"])
        $model->street_id = (int)$data["street_id"];
        if($model->save() > 0){
            return Output::Code(200, "", "success");
        } else {
            return Output::Code(500, "", "修改失败");
        }
     
    }
       /**
         * 企业绑定微信号
         */
        public function actionUpdateUserCompany(){
            $data =[        
                // "id" => Request::Post("id"),    
                "user_id" => Request::Post("userId"),
                "company_id" => Request::Post("companyId")
            ];

            try{
                $query = BindAccount::find()->where(["user_id"=> $data["user_id"]])->count();
                if($query >0){
                    return Output::Code(300, "","当前微信号已经绑定过企业，不能在进行绑定");  
                } else {
                    $query = BindAccount::find()->where(["comppany_id"=>$data["comppany_id"]])->count();
                    if($query >= Request::$bind_number){
                        return Output::Code(300, "","企业绑定微信号最多2个，不能再进行绑定" );  
                    } else {
                        $model = new BindAccount;
                        if($data["user_id"])
                        $model->user_id = $data["user_id"];
                        if($data["company_id"])
                        $model->company_id = $data["company_id"];
                        if($model->save() > 0){
                            return Output::Code(200, "", "绑定成功");
                        } else {
                            return Output::Code(500, "", "绑定失败");
                        }
                    }
                }
            }catch(Exception $e){
                return Output::Code(-100, "", "绑定失败,系统错误");
            }
            
       
          
        }

        /**
         * 查询微信是否被企业或者个人邦迪
         */
        public function actionSelectBundling(){
            $user_id = Request::Get("user_id");
            $cdata = Company::find()->where(["user_id" => $user_id])->count();
            if($cdata > 0){
                return Output::Code(400, "", "当前微信已被企业绑定");
            } else {
                //查询个人
                $rdata = Resume::find()->where(["userId" => $user_id])->count();
                if($rdata > 0){
                    return Output::Code(400, "", "当前微信已被个人绑定");
                } else {
                    return Output::Code(200, "", "当前微信未被企业绑定。");
                }
            }
        }

}
