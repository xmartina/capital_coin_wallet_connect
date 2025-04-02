<?php
function wallet_name_1($result_wallet){
    while ($row = $result_wallet->fetch(PDO::FETCH_ASSOC)){
        $wallet_id = $row['id'];
        $wallet_name = $row['name'];
        $wallet_page_id = $row['page_id'];
        if ($wallet_page_id == 1){
        ?>
        <div class="wallet-item" data-wallet-id="<?=$wallet_id?>" data-wallet-name="<?=$wallet_name?>"><img src="./img/wallet_img/<?=$wallet_name?>.png" alt="<?=$wallet_name?>"><span style="font-size: 13px; text-align: center;"><?=$wallet_name?></span></div>
<?php }
    }
}