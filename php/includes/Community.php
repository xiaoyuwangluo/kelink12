<?php
/**
 * 小宇网络 WAP建站系统 PHP版
 * 社区公共函数 (敏感词过滤 / 站点缓存清理 / 审核记录)
 */
if (!defined('KL_ROOT')) exit;

/**
 * 清理缓存文件 (缓存为全局文件，统一清理)
 */
if (!function_exists('kl_cache_clear_site')) {
    function kl_cache_clear_site() {
        if (!defined('KL_CACHE_DIR')) return;
        $files = glob(KL_CACHE_DIR . '/*.cache');
        if (!is_array($files)) return;
        foreach ($files as $file) {
            if (is_file($file)) @unlink($file);
        }
    }
}

/**
 * 内置敏感词列表 (来自 config.php 的 KL_Filter_All，竖线分隔)
 * @return array
 */
if (!function_exists('kl_filter_words')) {
    function kl_filter_words() {
        $words = [];
        if (defined('KL_Filter_All')) {
            foreach (explode('|', KL_Filter_All) as $w) {
                $w = trim($w);
                if ($w !== '') $words[] = $w;
            }
        }
        return $words;
    }
}

/**
 * 检测文本是否包含敏感词
 * 先按内置词 KL_Filter_All 匹配；传入 $db 时再读取当前站点自定义词库 WapFilter
 * @param string $text 待检测文本
 * @param Database|null $db 数据库实例 (可选)
 * @return bool true=命中敏感词
 */
if (!function_exists('kl_has_sensitive')) {
    function kl_has_sensitive($text, $db = null) {
        $text = (string)$text;
        if ($text === '') return false;

        foreach (kl_filter_words() as $word) {
            if (mb_stripos($text, $word) !== false) return true;
        }

        if ($db) {
            try {
                $rows = $db->getAll("SELECT word FROM WapFilter");
                foreach ($rows as $row) {
                    $word = trim((string)($row['word'] ?? ''));
                    if ($word !== '' && mb_stripos($text, $word) !== false) return true;
                }
            } catch (Exception $e) {
                // 表不存在等异常静默跳过，不影响发帖
            }
        }
        return false;
    }
}

/**
 * 写审核记录 (敏感词命中时调用)
 */
if (!function_exists('kl_audit_add')) {
    function kl_audit_add($db, $userid, $module, $contentid, $title, $content, $word = '') {
        try {
            $db->insert(
                "INSERT INTO WapAudit (userid,module,contentid,title,content,word,status,addtime) VALUES (?,?,?,?,?,?,0,NOW())",
                [(int)$userid, (string)$module, (int)$contentid, (string)$title, (string)$content, (string)$word]
            );
        } catch (Exception $e) {
            // 审核表不可用时静默跳过，不阻断发帖
        }
    }
}

/**
 * 写一条通知 (接收者与来源相同时跳过)
 */
if (!function_exists('kl_notice_add')) {
    function kl_notice_add($db, $uid, $fromuid, $ntype, $module, $contentid, $content) {
        $uid = (int)$uid;
        $fromuid = (int)$fromuid;
        if ($uid <= 0 || $uid === $fromuid) return;
        try {
            $db->insert(
                "INSERT INTO WapNotice (uid,fromuid,ntype,module,contentid,content,isread,addtime) VALUES (?,?,?,?,?,?,0,?)",
                [$uid, $fromuid, (string)$ntype, (string)$module, (int)$contentid, (string)$content, date('Y-m-d H:i:s')]
            );
        } catch (Exception $e) {
            // 通知表不可用时静默跳过，不阻断主流程
        }
    }
}

/**
 * 通知类型标签
 */
if (!function_exists('kl_notice_label')) {
    function kl_notice_label($ntype) {
        switch ((string)$ntype) {
            case 'reply':   return '回复';
            case 'mention': return '@提及';
            case 'like':    return '点赞';
            case 'follow':  return '关注';
            case 'friend':  return '好友';
            default:        return '通知';
        }
    }
}

/**
 * 通知类型标签 (扩展版：在原有标签基础上补充审核/订单类型)
 * 包装原 kl_notice_label，不改变原有行为
 */
if (!function_exists('kl_notice_label_ext')) {
    function kl_notice_label_ext($ntype) {
        if ((string)$ntype === 'audit') return '审核';
        if ((string)$ntype === 'order') return '订单';
        return kl_notice_label($ntype);
    }
}

/**
 * 点赞数 (replyid=0 表示主帖)
 */
if (!function_exists('kl_like_count')) {
    function kl_like_count($db, $module, $contentid, $replyid = 0) {
        try {
            return (int)$db->getScalar(
                "SELECT COUNT(*) FROM WapLike WHERE module=? AND contentid=? AND replyid=?",
                [(string)$module, (int)$contentid, (int)$replyid]
            );
        } catch (Exception $e) {
            return 0;
        }
    }
}

/**
 * 是否已点赞
 */
if (!function_exists('kl_like_exists')) {
    function kl_like_exists($db, $module, $contentid, $replyid, $userid) {
        try {
            $row = $db->getOne(
                "SELECT id FROM WapLike WHERE module=? AND contentid=? AND replyid=? AND userid=?",
                [(string)$module, (int)$contentid, (int)$replyid, (int)$userid]
            );
            return (bool)$row;
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * 单向拉黑判断：$userid 是否拉黑了 $blockuid
 */
if (!function_exists('kl_block_exists')) {
    function kl_block_exists($db, $userid, $blockuid) {
        try {
            $row = $db->getOne("SELECT id FROM WapBlock WHERE userid=? AND blockuid=?",
                [(int)$userid, (int)$blockuid]);
            return (bool)$row;
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * 双向拉黑判断：任一方拉黑另一方即视为不可互动
 */
if (!function_exists('kl_is_blocked')) {
    function kl_is_blocked($db, $userid, $otheruid) {
        if ((int)$userid <= 0 || (int)$otheruid <= 0) return false;
        return kl_block_exists($db, $userid, $otheruid)
            || kl_block_exists($db, $otheruid, $userid);
    }
}
