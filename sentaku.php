<html>
    <head><title>洗濯ページ</title></head>　
    <meta charset="UTF-8">
    <h1>洗濯a<a href=""></a></h1>
    <?php
    function h($str) { return htmlspecialchars($str, ENT_QUOTES, "UTF-8"); }
    require_once("lib/db.php");
    $db = connectDB();

    $clothes_id_list = array(10, 13, 15);
    $laundry_tags = array();

    //洗濯するタグのリスト
    for($i = 0; $i < count($clothes_id_list); ++$i){
        $laundry_tags[$i] = array();
        $clothes_id = $clothes_id_list[$i];
        $result_tag_id=$db->query("SELECT tag_id FROM clothes_tags WHERE clothes_id='$clothes_id'");
        //その服についているタグを全て取得
        for($j = 0; $row=$result_tag_id->fetch(); ++$j){
            $reration_tag = $row['tag_id'];
            $result_reration_tag_id=$db->query("SELECT tag_id2 FROM tag_incompatible_ralations WHERE tag_id1='$reration_tag'");
            //そのタグと組み合わせてはいけないタグを全て取得
            for($n = 0; $row_reration=$result_reration_tag_id->fetch(); ++$n){
                $reration_clothes_id = $row_reration['tag_id2'];
                $result_reration_clothes_id=$db->query("SELECT clothes_id FROM clothes_tags WHERE tag_id='$reration_clothes_id'");
                //組み合わせてはいけないタグを持っている服のidを取得
                for($o = 0; $row_reration_clothes_id=$result_reration_clothes_id->fetch(); ++$o){
                    //その服のidが今回の洗濯リストに含まれているか確認する
                    for($k = 0; $k < count($clothes_id_list); ++$k){
                        if($clothes_id_list[$k] == $row_reration_clothes_id['clothes_id']){
                            $laundry_tags[$i][] = $k;
                        }
                    }
                }
            }
        }
    }
    var_dump($laundry_tags[0]);
    var_dump($laundry_tags[1]);
    var_dump($laundry_tags[2]);
    ?>
</html>