<?php
namespace graph;

// グラフを受け取ってノードのグループを返す
function welchPowell($g) {
    $n = count($g);

    $matrix = array();
    for ($i = 0; $i < $n; $i++) {
        for ($j = 0; $j < $n; $j++) {
            $matrix[$i][$j] = false;
        }
    }

    $degrees = array();
    for ($i = 0; $i < $n; $i++) {
        $degrees[$i] = 0;
    }

    foreach ($g as $v1=>$edges) {
        foreach ($edges as $v2) {
            if (!$matrix[$v1][$v2]) {
                $degrees[$v1]++;
            }

            if (!$matrix[$v2][$v1]) {
                $degrees[$v2]++;
            }

            $matrix[$v1][$v2] = true;
            $matrix[$v2][$v1] = true;
        }
    }

    arsort($degrees);

    $colors = array();

    $c = 0;
    while (count($colors) < $n) {
        // $cで塗ったノード
        $filled = array();

        foreach ($degrees as $i=>$_degree) {
            if (isset($colors[$i])) {
                continue;
            }

            $fill = true;
            foreach($filled as $v) {
                if ($matrix[$v][$i]) {
                    $fill = false;
                    break;
                }
            }

            if ($fill) {
                $colors[$i] = $c;
                $filled[] = $i;
            }
        }
        $c++;
    }

    return $colors;
}
?>
