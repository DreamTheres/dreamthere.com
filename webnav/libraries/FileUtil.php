<?php
/**
 * 文件操作类
 */
class FileUtil {

    /**
     * 添加模式写文件（a）
     * @param type $file    文件地址
     * @param type $context 写入内容
     */
    public static function write2txt($file, $context) {
        $myfile = fopen($file, "a") or die("Unable to open file!");

        fwrite($myfile, $context . PHP_EOL);

        fclose($myfile);
    }

    /**
     * 覆盖模式写文件（w）
     * @param type $file    文件地址
     * @param type $msg     写入内容
     */
    public static function writeMsg2txt($file, $msg) {
        $myfile = fopen($file, "w") or die("Unable to open file!");

        fwrite($myfile, $msg . PHP_EOL);

        fclose($myfile);
    }

    /**
     * 只读模式读文件（r）
     * @param type $file    文件地址
     * @return type         返回文件内容（全部）
     */
    public static function read4txt($file) {
        $myfile = fopen($file, "r") or die("Unable to open file!");

        $str = fread($myfile, filesize($file));

        fclose($myfile);

        return $str;
    }

    /**
     * 迭代获取目录下的所有文件
     * @param string $dir   目录路径
     * @return array        文件路径列表数组
     * <br/><br/>
     * 注意:编码转换 iconv('utf-8', 'gb2312', 'D:\我的文档\下载')
     */
    public static function getAllFile($dir) {
        $dirs = fileUtil::getDir($dir);
        $files = fileUtil::getFile($dir);
        foreach ($dirs as $dirx) {
            $files = array_merge($files, fileUtil::getAllFile($dirx));
        }
        return $files;
    }

    /**
     * 获取目录下的所有文件夹
     * @param string $dir   目录路径，如：E:\ 或 ./dir/
     * @param array $notIn  排查目录数组，如：array('.', '..', '.svn', 'test')
     * @return array        文件夹路径数组
     * <br/><br/>
     * 注意:编码转换 iconv('utf-8', 'gb2312', 'D:\我的文档\下载')
     */
    public static function getDir($dir, $notIn = array('.', '..')) {
        $dirArray = array();

        if (!is_dir($dir)) {//不是目录直接返回
            return $dirArray;
        }

        $dir = trim($dir, '\\');
        $dir = trim($dir, '/');

        if (false != ($handle = opendir($dir))) {
            while (false !== ($file = readdir($handle))) {
                if (is_dir($dir . "/" . $file) && !in_array($file, $notIn)) {
                    $dirArray[] = $dir . "/" . $file;
                }
            }
            //关闭句柄
            closedir($handle);
        }
        return $dirArray;
    }

    /**
     * 获取目录路径下的文件列表
     * @param string $dir       目录路径，如：E:\ 或 ./dir/
     * @param array $notIn      排除文件数组，如：array('test.txt','123.text') 默认空
     * @return array            文件路径数组
     * <br/><br/>
     * 注意:编码转换 iconv('utf-8', 'gb2312', 'D:\我的文档\下载')
     */
    public static function getFile($dir, $notIn = array()) {
        $fileArray = array();

        if (!is_dir($dir)) {//不是目录直接返回
            return $fileArray;
        }

        $dir = trim($dir, '\\');
        $dir = trim($dir, '/');

        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {
                if (is_file($dir . "/" . $file) && !in_array($file, $notIn)) {
                    $fileArray[$i] = $dir . "/" . $file;
                    $i++;
                }
            }
            //关闭句柄
            closedir($handle);
        }
        return $fileArray;
    }

    /**
     * 解压文件
     * @param string $zipFile       压缩包文件路径
     * @param string $toDir         解压到目录路径
     * @return boolean
     */
    public static function unZipFile($zipFile, $toDir = '') {
        if (!file_exists($zipFile)) {
            return false;
        }

        $zip = new ZipArchive();
        $result = $zip->open($zipFile);
        if ($result === TRUE) {
            if (empty($toDir)) {
                $toDir = dirname($zipFile);
            }
            $zip->extractTo($toDir);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

}
