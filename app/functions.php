<?php

/**
 * 将时间转化为时间段
 * @param $now
 * @return string
 */
function parseTimeLine($now): string
{
    $time_line = time() - strtotime($now);
    if ($time_line <= 0) {
        return '刚刚';
    } elseif ($time_line < 60) {
        return $time_line . '秒前';
    } elseif ($time_line < 60 * 60) {
        return floor($time_line / 60) . '分钟前';
    } elseif ($time_line < 60 * 60 * 24) {
        return floor($time_line / (60 * 60)) . '小时前';
    } elseif ($time_line < 60 * 60 * 24 * 7) {
        return floor($time_line / (60 * 60 * 24)) . '天前';
    } elseif ($time_line < 60 * 60 * 24 * 7 * 4) {
        return floor($time_line / (60 * 60 * 24 * 7)) . '周前';
    } elseif ($time_line < 60 * 60 * 24 * 7 * 4 * 12) {
        return floor($time_line / (60 * 60 * 24 * 7 * 4)) . '个月前';
    } else {
        return floor($time_line / (60 * 60 * 24 * 7 * 4 * 12)) . '年前';
    }
}