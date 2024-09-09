<?php

class OsCouponModel extends OsModel{
  var $id,
      $code,
      $name,
      $description,
      $rules,
      $status,
      $discount_type,
      $discount_value,
      $updated_at,
      $created_at;

  function __construct($id = false){
    $this->table_name = LATEPOINT_TABLE_COUPONS;
    $this->nice_names = array(
                              'code' => __('Coupon Code', 'latepoint-pro-features'));

    parent::__construct($id);
  }

  public function get_rule($rule){
    if($this->rules){
      $rules_arr = json_decode($this->rules, true);
      if(isset($rules_arr[$rule])) return $rules_arr[$rule];
    }
    return false;
  }


  protected function before_save(){
    $this->code = trim(strtoupper($this->code));
  }

  protected function get_default_status(){
    return LATEPOINT_COUPON_STATUS_ACTIVE;
  }

  protected function before_create(){
    if(empty($this->status)) $this->status = $this->get_default_status();
  }

  

  protected function allowed_params($role = 'admin'){
    $allowed_params = array('id',
                            'code',
                            'name',
                            'description',
                            'rules',
                            'status',
                            'discount_type',
                            'discount_value',
                            'updated_at',
                            'created_at');
    return $allowed_params;
  }

  protected function params_to_save($role = 'admin'){
    $params_to_save = array('id',
                            'code',
                            'name',
                            'description',
                            'rules',
                            'status',
                            'discount_type',
                            'discount_value',
                            'updated_at',
                            'created_at');
    return $params_to_save;
  }

  protected function properties_to_validate(){
    $validations = array(
      'code' => array('presence', 'uniqueness'),
      'status' => array('presence'),
      'discount_type' => array('presence'),
      'discount_value' => array('presence')
    );
    return $validations;
  }
}