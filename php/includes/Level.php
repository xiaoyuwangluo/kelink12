<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 会员等级 (按积分计算)
 */
if (!defined('KL_ROOT')) exit;

/**
 * 等级阈值表: [等级, 名称, 起始积分]
 */
if (!function_exists('kl_level_table')) {
    function kl_level_table() {
        return [
            1 => ['level' => 1, 'name' => '新手上路', 'point' => 0],
            2 => ['level' => 2, 'name' => '初出茅庐', 'point' => 100],
            3 => ['level' => 3, 'name' => '小有名气', 'point' => 300],
            4 => ['level' => 4, 'name' => '社区达人', 'point' => 800],
            5 => ['level' => 5, 'name' => '资深前辈', 'point' => 2000],
            6 => ['level' => 6, 'name' => '传奇元老', 'point' => 5000],
        ];
    }
}

/**
 * 根据积分计算等级信息
 * @param int $point 积分
 * @return array level/name/current(本级已获得的进度积分)/need(本级跨度)/next(距下一级所需积分)/percent(本级进度百分比)
 */
if (!function_exists('kl_level_info')) {
    function kl_level_info($point) {
        $point = (int)$point;
        if ($point < 0) $point = 0;
        $table = kl_level_table();
        $maxLevel = max(array_keys($table));
        $current = $table[1];
        foreach ($table as $lv => $item) {
            if ($point >= $item['point']) $current = $item;
        }
        $level = (int)$current['level'];
        $base = (int)$current['point'];

        if ($level >= $maxLevel) {
            return [
                'level'   => $level,
                'name'    => $current['name'],
                'current' => $point - $base,
                'need'    => 0,
                'next'    => 0,
                'percent' => 100,
            ];
        }

        $nextBase = (int)$table[$level + 1]['point'];
        $span = $nextBase - $base;
        $got = $point - $base;
        $percent = $span > 0 ? (int)round($got * 100 / $span) : 100;
        if ($percent > 100) $percent = 100;
        if ($percent < 0) $percent = 0;

        return [
            'level'   => $level,
            'name'    => $current['name'],
            'current' => $got,
            'need'    => $span,
            'next'    => $nextBase - $point,
            'percent' => $percent,
        ];
    }
}

/**
 * 等级标签 HTML，如 [Lv3 小有名气]
 */
if (!function_exists('kl_level_tag_html')) {
    function kl_level_tag_html($point) {
        $info = kl_level_info($point);
        return '[Lv' . (int)$info['level'] . ' ' . $info['name'] . ']';
    }
}
