<?php

/**
 * 将时间转化为时间段
 * @param string $now
 * @return string
 */
function parseTimeLine(string $now): string
{
    try {
        $now = new DateTime($now);

        $diff = $now->diff(new DateTime());

        if ($diff->y > 0) {
            return $diff->y . '年前';
        } elseif ($diff->m > 0) {
            return $diff->m . '个月前';
        } elseif ($diff->d > 0) {
            return $diff->d . '天前';
        } elseif ($diff->h > 0) {
            return $diff->h . '小时前';
        } elseif ($diff->i > 0) {
            return $diff->i . '分钟前';
        } else {
            return '刚刚';
        }
    } catch (Exception $exception) {
        return '未知';
    }
}