<?php
namespace clothes_group;

include_once(dirname(__FILE__) .  "/prelude.php");
include_once(dirname(__FILE__) .  "/graph.php");

function group($db, $clothes) {
    $stat = $db->prepare('SELECT * FROM clothes_tags WHERE EXISTS (SELECT * FROM tags WHERE tags.id = clothes_tags.tag_id AND deleted_at IS NULL) AND clothes_id IN ' . array_prepare_query('clothes_id', count($clothes)));
    array_prepare_bind($stat, 'clothes_id', array_column($clothes, 'id'), \PDO::PARAM_INT);
    $stat->execute();
    $clothes_tags = $stat->fetchAll();
    $tagIds = array_unique(array_column($clothes_tags, 'tag_id'));

    $clothes_to_tags = [];
    foreach ($clothes as $c) {
        $clothes_to_tags[$c['id']] = [];
    }
    foreach ($clothes_tags as $ct) {
        $clothes_to_tags[$ct['clothes_id']][] = $ct['tag_id'];
    }

    $tag_to_clothes = [];
    foreach ($tagIds as $t) {
        $tag_to_clothes[$t] = [];
    }
    foreach ($clothes_tags as $ct) {
        $tag_to_clothes[$ct['tag_id']][] = $ct['clothes_id'];
    }

    $stat = $db->prepare('SELECT * FROM tag_incompatible_ralations WHERE tag_id1 IN ' . array_prepare_query('tag_id1', count($tagIds)) . ' OR tag_id2 IN ' . array_prepare_query('tag_id2', count($tagIds)));
    array_prepare_bind($stat, 'tag_id1', $tagIds, \PDO::PARAM_INT);
    array_prepare_bind($stat, 'tag_id2', $tagIds, \PDO::PARAM_INT);
    $stat->execute();
    $relations = $stat->fetchAll();

    $tag_to_relations = [];
    foreach ($tagIds as $t) {
        $tag_to_relations[$t] = [];
    }
    foreach ($relations as $r) {
        $tag_to_relations[$r['tag_id1']][] = $r['tag_id2'];
        $tag_to_relations[$r['tag_id2']][] = $r['tag_id1'];
    }

    $index = 0;
    $cid_to_index = [];
    $index_to_cid = [];
    $welchPowellInput = [];
    foreach ($clothes as $c) {
        $cid_to_index[$c['id']] = $index;
        $index_to_cid[$index] = $c['id'];
        $welchPowellInput[$index] = [];
        $index++;
    }

    foreach ($relations as $r) {
        $tag_id1 = $r['tag_id1'];
        $tag_id2 = $r['tag_id2'];
        if (isset($tag_to_clothes[$tag_id1]) && isset($tag_to_clothes[$tag_id2])) {
            foreach ($tag_to_clothes[$tag_id1] as $cid1) {
                foreach ($tag_to_clothes[$tag_id2] as $cid2) {
                    $welchPowellInput[$cid_to_index[$cid1]][] = $cid_to_index[$cid2];
                }
            }
        }
    }

    $welchPowellResult = \graph\welchPowell($welchPowellInput);
    $result = [];
    foreach ($welchPowellResult as $i => $group) {
        $result[$index_to_cid[$i]] = $group;
    }

    return $result;
}
?>
