<?php
include_once('./_common.php');
include_once('../../util/purchase_proc.php');

$today = date("Y-m-d H:i:s",time());
$todate = date("Y-m-d",time());

if($_GET['debug']) $debug = 1;

if($_POST['nw_soodang_reset'] == 'on'){
    $trunc1 = sql_query(" TRUNCATE TABLE `soodang_pay` ");

    $trunc14 = sql_query(" TRUNCATE TABLE `brecom_bonus_noo` ");
    $trunc14 = sql_query(" TRUNCATE TABLE `brecom_bonus_today` ");
    $trunc14 = sql_query(" TRUNCATE TABLE `brecom_bonus_week` ");

    $trunc15 = sql_query(" TRUNCATE TABLE `recom_bonus_noo` ");
    $trunc15 = sql_query(" TRUNCATE TABLE `recom_bonus_today` ");
    $trunc15 = sql_query(" TRUNCATE TABLE `recom_bonus_week` ");

    $trunc16 = sql_query(" TRUNCATE TABLE `iwol` ");

    $member_update_sql = " UPDATE g5_member set  mb_balance = 0 WHERE mb_level < 9 ";
    sql_query($member_update_sql);
    

    if($trunc16){
        $result = 1;
    }
}

if($_POST['nw_member_reset'] == 'on'){

    $trunc5 = sql_query(" TRUNCATE TABLE `g5_shop_order` ");
    $trunc6 = sql_query(" TRUNCATE TABLE `package_log`; ");
    $trunc8 = sql_query(" TRUNCATE TABLE `package_p1`; ");
    $trunc9 = sql_query(" TRUNCATE TABLE `package_p2`; ");
    $trunc10 = sql_query(" TRUNCATE TABLE `package_p3` ");
    $trunc11 = sql_query(" TRUNCATE TABLE `package_p4` "); 
    $trunc12 = sql_query(" TRUNCATE TABLE `package_p5` "); 
    $trunc13 = sql_query(" TRUNCATE TABLE `package_p6` "); 
    $trunc14 = sql_query(" TRUNCATE TABLE `package_p7` "); 
    $trunc15 = sql_query(" TRUNCATE TABLE `rank` ");

    $member_update_sql = " UPDATE g5_member set  mb_deposit_point = 0, mb_deposit_calc=0, mb_balance = 0,mb_save_point=0, mb_rate=0, sales_day='0000-00-00', rank_note='' WHERE mb_level < 9 ";
    sql_query($member_update_sql);

    $sql_member_reset2 = " UPDATE g5_member set grade = 0, mb_level = 0 WHERE mb_no > 1 ";
    sql_query($sql_member_reset2);
    
    if($sql_member_reset2){
        $result = 1;
    }
}

if($_POST['nw_asset_reset'] == 'on'){

    $trunc2 = sql_query(" TRUNCATE TABLE `{$g5['widthdrawal']}` ");
    $trunc3 = sql_query(" TRUNCATE TABLE `{$g5['deposit']}` ");

    if($trunc3){
        $result = 1;
    }
}

if($_POST['nw_data_test'] == 'on'){
    
    $mb_deposit_point = 10;
    $member_update_sql = " UPDATE g5_member set mb_deposit_point = {$mb_deposit_point}, mb_deposit_calc = 0 WHERE mb_no > 0 ";
    $update_member = sql_query($member_update_sql);
    
   if($update_member){

    
    $insert_order_sql = " INSERT INTO `g5_shop_order` (od_id, mb_id, mb_no, od_cart_price, upstair, od_cash, od_name, od_tno, pv, od_time, od_date, od_settle_case, od_email, od_tel, od_hp, od_zip1, od_zip2, od_addr1, od_addr2, od_addr3, od_addr_jibeon, od_b_name, od_b_tel, od_b_hp, od_b_zip1, od_b_zip2, od_b_addr1, od_b_addr2, od_b_addr3, od_b_addr_jibeon, od_memo, od_cart_count, od_cart_coupon, od_send_cost, od_send_coupon, od_receipt_price, od_cancel_price, od_receipt_point, od_receipt_cash, od_refund_price, od_bank_account, od_receipt_time, od_coupon, od_misu, od_shop_memo, od_mod_history, od_status, od_hope_date, od_test, od_mobile, od_pg, od_app_no, od_escrow, od_casseqno, od_tax_flag, od_tax_mny, od_vat_mny, od_free_mny, od_delivery_company, od_invoice, od_invoice_time, od_cash_no, od_cash_info, od_pwd, od_ip) VALUE " ;

    for($i=0; $i <= 10 ; $i++){
        $orderid = date("YmdHis",time()).mt_rand(0000,9999);
        $member_id = 'test'.($i+20);
        $logic = purchase_package($member_id,2021011491,1);
        $insert_order_sql_arry .= " ({$orderid}, '{$member_id}', 0, 246, 1.05688, 232.76, 'M1', 2021011491, 1230, '{$today}', '{$todate}', 'eth', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '{$today}', 0, 0, NULL, NULL, '패키지구매', '0000-00-00', 0, 0, '', '', 0, '', 0, 0, 0, 0, '0', '', '0000-00-00 00:00:00', NULL, NULL, '', ''),";
    }

    $result_insert_sql = substr($insert_order_sql.$insert_order_sql_arry, 0, -1);

    if($debug){
        print_R($result_insert_sql);
        $result = 1;
    }else{
        $result = sql_query($result_insert_sql);
    }
   }
}



if($_POST['nw_data_del'] == 'on'){
    
    $del_member = " DELETE from `g5_member` WHERE mb_no > 1; ";
    
    if($debug){
        print_R($del_member);
        $del_result = 1;
    }else{ 
        $del_result = sql_query($del_member);
    }


    if($del_result){
        $alter_table_query = " ALTER TABLE `g5_member` set AUTO_INCREMENT = 2; ";

        if($debug){
            print_R($alter_table_query);
        }else{ 
            sql_query($alter_table_query);
        }
        
    }
    
}

if($debug){}else{
    if($result){
        alert('정상 처리되었습니다.');
        goto_url('./config_reset.php');
    }
}
?>