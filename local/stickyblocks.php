<?php

/***

This function returns the custom sticky blocks definition

todo: this could be written more elegantly, but no hurry...

**/


function get_custom_stickyblocks() {

    $blocks = array();

    //Learning Path sticky blocks
    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'tao_course_status');
    $pinnedblock->pagetype ='format_learning';
    $pinnedblock->weight = '0';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'tao_certification_path');
    $pinnedblock->pagetype ='format_learning';
    $pinnedblock->weight = '1';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;
    
    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'tao_team_groups');
    $pinnedblock->pagetype ='format_learning';
    $pinnedblock->weight = '2';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'tao_group_activities');
    $pinnedblock->pagetype ='format_learning';
    $pinnedblock->weight = '3';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'rafl_contributors');
    $pinnedblock->pagetype ='format_learning';
    $pinnedblock->weight = '4';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'admin');
    $pinnedblock->pagetype ='format_learning';
    $pinnedblock->position = 'l';
    $pinnedblock->weight = '0';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    //My collaboration sticky blocks
    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'tao_my_neighbours');
    $pinnedblock->pagetype ='my-collaboration';
    $pinnedblock->weight = '0';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'tags');
    $pinnedblock->pagetype ='my-collaboration';
    $pinnedblock->weight = '1';
    $pinnedblock->blockid = $id;
    //set the number of tags to show to 20
    $blockconfig = new stdclass;
    $blockconfig->title = get_string('tags', 'tag');
    $blockconfig->numberoftags = 20;

    $pinnedblock->configdata = base64_encode(serialize($blockconfig));
    $blocks[] = $pinnedblock;

    $pinnedblock = new_stickyblock_def();
    $id = get_field('block', 'id', 'name', 'messages');
    $pinnedblock->pagetype ='my-collaboration';
    $pinnedblock->weight = '2';
    $pinnedblock->blockid = $id;
    $blocks[] = $pinnedblock;

    return $blocks;

}

/*
returns basic $pinnedblock object with parameters that don't change
*/

function new_stickyblock_def() {

    $obj = new stdclass();
    $obj->position='r';
    $obj->visible = '1';
    $obj->configdata = '';

    return $obj;

}

?>
