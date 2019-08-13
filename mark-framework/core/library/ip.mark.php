<?php
defined('IS_MARKPHP') || die('Access Denied!');

class IpMark {

    //IP-获取用户IP
    public function get_ip() {
        static $realip = null;

        if (null !== $realip) {
            return $realip;
        }
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } else if (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        
        // 处理多层代理的情况
        if (false !== strpos($realip, ',')) {
            $realip = reset(explode(',', $realip));
        }
        
        // IP地址合法验证
        $realip = filter_var($realip, FILTER_VALIDATE_IP, null);
        if (false === $realip) {
            return '0.0.0.0';   // unknown
        }
        return $realip;
    }

    //检查IP是否在指定的网络范围内
    public function ip_in_range($ip, $range) {
                
        if (strpos($range, '/') !== false) {
            // CIDR值
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) {
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ((ip2long($ip) & $netmask_dec) === (ip2long($range) & $netmask_dec));
            } else {
                $x = explode('.', $range);
                while (count($x) < 4) {
                    $x[] = '0';
                }
                list($a, $b, $c, $d) = $x;
                $range = sprintf('%u.%u.%u.%u', empty($a) ? '0' : $a, empty($b) ? '0' : $b, empty($c) ? '0' : $c, empty($d) ? '0' : $d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);

                $wildcard_dec = pow(2, (32 - $netmask)) - 1;
                $netmask_dec = ~ $wildcard_dec;

                return (($ip_dec & $netmask_dec) === ($range_dec & $netmask_dec));
            }
        } else {
            // 通配符格式，转换为IP段进行判断
            if (strpos($range, '*') !== false) {
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "{$lower}-{$upper}";
            }

            // IP段
            if (strpos($range, '-') !== false) {
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float) sprintf('%u', ip2long($lower));
                $upper_dec = (float) sprintf('%u', ip2long($upper));
                $ip_dec = (float) sprintf('%u', ip2long($ip));
                return (($ip_dec >= $lower_dec) && ($ip_dec <= $upper_dec));
            }

            // 网络范围格式不正确，返回false
            return false;
        }
    }

}
