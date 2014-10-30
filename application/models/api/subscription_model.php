<?php

class Subscription_model extends CI_Model{
       
    function __construct()
    {
       parent::__construct();	
       $this->load->database();
       $parent=array();
    }
    
    function getlist()
    {
        $this->db->select('d.name,d.days,p.duration_id as subscription_id,p.content_id,p.content_type as type,p.price');
        $this->db->from('duration d');
        $this->db->join('price p','d.id=p.duration_id','inner');
        $this->db->where('d.uid',$this->owner_id);
        $query = $this->db->get();
       //echo $this->db->last_query();
       return $query->result();
    }
    
     
    function getSubsIdArr($invoice)
    {
	$this->db->select('order_details.subscription_id as subscription_id, order_details.order_id as order_id, duration.days as validtime');
        $this->db->from('order_details');
        $this->db->join('order','order_details.order_id = order.id', 'left');
	$this->db->join('price','order_details.subscription_id = price.id', 'left');
	$this->db->join('duration','price.duration_id = duration.id', 'left');
        $this->db->where('order.invoice', $invoice);
        $query = $this->db->get();
        $result = $query->result();
	return $result;
    }
    
    function save_order($data){       
        $this->db->set($data);
        $this->db->set('created','NOW()',FALSE);
        $this->db->insert('order');
        return $this->db->insert_id();
    }
    
    function saveOrder($data)
    { 
	if(isset($data['txn_id'])){
	    $orderData = array(
		'trasaction_id'=>$data['txn_id'],
		'status'=>strtolower($data["payment_status"])
	    );
	    $this->db->set('modified','NOW()',FALSE);
	    $this->db->where('invoice', $data['invoice']);
	    $this->db->update('order', $orderData); 		
	    return $data['order_id'];
	} else {
            //return 'ss';die;
	    $orderData = array(
		'user_id'=>$data['user_id'],
		'invoice'=>$data['invoice'],
		'total_amount'=>$data['total_amount'],		
		'status'=>'pending'
	    );      
	    $this->db->set($orderData);
	    $this->db->set('created','NOW()',FALSE);	    
	    $this->db->insert('order');
	    return $this->db->insert_id();  
	}
       // return $orderData;die;
    }    
    
    function saveOrderDetails($data)
    {
	if(isset($data['txn_id'])){
	    $subscriptionDataArr = $this->getSubsIdArr($data['invoice']);
	    $countData = count($subscriptionDataArr);
	    for($i=0; $i<$countData; $i++){
		$this->db->set('start_date','NOW()',FALSE);
		//echo $endDate = DATE_ADD('NOW()', 'INTERVAL '.$subscriptionDataArr[$i]->validtime. 'DAY');exit;
		$this->db->set('end_date','NOW() + INTERVAL '.$subscriptionDataArr[$i]->validtime. ' DAY',FALSE);
		$this->db->set('modified','NOW()',FALSE);
		$this->db->where('order_id', $subscriptionDataArr[$i]->order_id);
		$this->db->where('subscription_id', $subscriptionDataArr[$i]->subscription_id);
		$this->db->update('order_details');
	    }
	    return true ;
	} else {
	    $orderDetails = array(
		'order_id'=>$data['order_id'],
		'subscription_id'=>$data['subscription_id'],
		'amount'=>$data['amount']
		//'discount_amount'=>$data['discount_amount']
	    );
	    $this->db->set($orderDetails);
	    $this->db->set('created','NOW()',FALSE);	    
	    $this->db->insert('order_details');
	    return true ;  
	}
    }
    
    function updateOrderStatus($invoice_no, $status)
    {
	$this->db->set('status',$status);
	$this->db->set('modified','NOW()',FALSE);
	$this->db->where('invoice', $invoice_no);
	$this->db->update('order'); 		
	return true ;
    }
    
    function delete_order($id)
    {
        $this->db->where('order_id',$id);
        $this->db->delete('order_details');
        
        $this->db->delete('order');
    }
}


?>