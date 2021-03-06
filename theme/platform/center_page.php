<?
	include_once('./_common.php');
    include_once(G5_THEME_PATH.'/_include/wallet.php');
	include_once(G5_THEME_PATH.'/_include/gnb.php');

    function active_check($val, $target){
        $bool_check = $_GET[$target];
        if($bool_check == $val){
            return " active ";
        }
    }

    if (empty($_GET['fr_date'])) $fr_date = date("Y-m-d", strtotime(date("Y-m-d")."-7 day"));
    if (empty($to_date)) $to_date = date("Y-m-d", strtotime(date("Y-m-d")."+1 day"));
    
    $select_id = $member['mb_id'];
    
    $qstr = "id=center_page&"."fr_date=".$fr_date."&amp;to_date=".$to_date."&amp;center=".$select_id;
    $query_string = $qstr ? '?'.$qstr : '';
    
    $sql_common = " FROM g5_member WHERE mb_center = '{$select_id}' ";

    $sql = " select count(*) as cnt ".$sql_common;
    
    $row_cnt = sql_fetch($sql);
    $total_count = $row_cnt['cnt'];
    
    $rows = 30;
    $total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
    if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
    $from_record = ($page - 1) * $rows; // 시작 열을 구함
    
    $sql = " select * ".$sql_common. " order by mb_no asc limit {$from_record}, {$rows}";
    $result = sql_query($sql);

    // 센터수당 
    $center_bonus_rate_val = sql_fetch("SELECT rate FROM wallet_bonus_config WHERE code = 'center' ")['rate'];
    $center_bonus_rate = $center_bonus_rate_val * 0.04;
?>


<link type="text/css" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.4/themes/base/jquery-ui.css" rel="stylesheet">
<script>
$(function($){
    $.datepicker.regional["ko"] = {
        closeText: "close",
        prevText: "이전달",
        nextText: "다음달",
        currentText: "오늘",
        monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
        monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
        dayNames: ["일","월","화","수","목","금","토"],
        dayNamesShort: ["일","월","화","수","목","금","토"],
        dayNamesMin: ["일","월","화","수","목","금","토"],
        weekHeader: "Wk",
        dateFormat: "yymmdd",
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        yearSuffix: ""
    };
	$.datepicker.setDefaults($.datepicker.regional["ko"]);
    $("#fr_date, #to_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, maxDate: "+0d" });
});
</script>

<style>
    .content-box +.content-box{margin-top:10px;}
    .center_box .box-header{line-height: 30px;}
    .center_box .box-header span{display: inline-block;}
    .center_box .lvl-icon{width: 30px;height: 30px;line-height:30px;vertical-align: middle;}
    .center_box .lvl-icon i{vertical-align: top;}
    .center_box .user_id{font-size:18px;font-weight:600;line-height:30px;vertical-align: top;}
    .center_box .mygrade{line-height: 10px;font-size:10px;font-weight: 300;vertical-align:baseline;padding:2px 8px}

    .center_box #detail .sales{color:black;}
    .center_box #detail dd{margin-right:10px;font-size:12px;color:#333;}
    .pg_page:hover{background:white;outline:1px solid white}
    
</style>


<?include_once(G5_THEME_PATH.'/_include/breadcrumb.php');?>
    <main>
        <div class='container bonus_history'>
            <!-- SEARCH -->
            <section class="col-sm-12 col-12 mt30 mb30" id="search-container">
                <form name="fsearch" id="fsearch" action="/page.php" method="GET">
                    <input type="hidden" name="id" id="" value="center_page">
                    <input type="hidden" name="stx" id="stx" value="">
                    <div class="row">
                        <li class="col-5"><input type="text" id="fr_date" name="fr_date" class='date_picker' placeholder="Date range from" value=<?=$fr_date?> /></li>
                        <li class="col-5"><input type="text" id="to_date" name="to_date" class='date_picker' placeholder="Date range to" value=<?=$to_date?> /></li>
                        <li class="col-2"><button type='button' class="btn wd inline blue filter_btn b_skyblue_round" onclick="search_submit();"><i class="ri-search-line"></i></button></li>
                    </div>
                </form>
            </section>
            
            <!-- //SEARCH -->
            
            <?php if($total_count < 1) {?>
                <div class="no_data box_on">하부 센터회원이 존재하지 않습니다</div>
            <?}?>
                
                <? while($row = sql_fetch_array($result)){
                    

                    $order_total_sql = "SELECT sum(upstair) as upstair_total from g5_shop_order WHERE mb_id = '{$row['mb_id']}' AND od_date >= '{$fr_date}' AND od_date < '{$to_date}' ";
                    $order_total = sql_fetch($order_total_sql);
                    
                    // $bg = 'bg'.($i%2);
                    $total_hap += $order_total['upstair_total'];
                    $center_bonus = $order_total['upstair_total']*$center_bonus_rate;
                    $total_center_bonus += $center_bonus ;
                ?>
    
                <div class="col-sm-12 col-12 content-box round center_box" id="">

                    <div class="box-header row">
                        <div class='col-7 text-left'>
                            <span class='lvl-icon'><?=user_icon($row['mb_id'],'icon')?></span>
                            <span class='user_id'><?=$row['mb_id']?></span>
                            <!-- <span class="mygrade badge color<?=$row['grade']?>"><?=$row['grade']?> STAR</span> -->
                        </div>

                        <div class='col-5 text-right'>
                            <span class='d_sum center_p font_skyblue'> $ <?=shift_auto_zero($center_bonus)?></span>
                            <!-- <span class='btn inline caret'><i class="ri-arrow-down-s-line"></i></span> -->
                        </div>
                    </div>

                    <div class='inblock row' id="detail" >
                        <div class='col-8 text-left' style="padding-right:0">
                            <dd>가입일 : <?=$row['mb_open_date']?> | 추천인 : <?=$row['mb_recommend']?></dd>
                            
                        </div>
                        <div class='col-4 text-right nopadding'>
                            <!-- <dd>기간 PV : <?=Number_format($total_pv)?> 원</dd> -->
                            <dd class='d_sum sales' style="padding-right:5px">기간 매출 : $ <?=shift_auto_zero($order_total['upstair_total'])?></dd>
                        </div>
                    </div>

                </div>
                <?}?>
            <!-- // 수당 -->
            <? $pagelist = get_paging($config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr");
                echo $pagelist;
            ?>
           
    </main>

    <?php include_once(G5_THEME_PATH.'/_include/tail.php'); ?>

</section>

<script>
	$(function(){
		$(".top_title h3").html("<span data-i18n=''>센터회원관리</span>")
	});

    window.onload = function(){
    move(<?=$bonus_per?>);
    }


    function search_submit(act = null)
    {
        // console.log('search');
        var f = document.fsearch;
        f.stx.value = act;
        f.submit();
    }

</script>

