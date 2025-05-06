<?php

namespace plugin\acms\app\admin\controller;

use Exception;
use support\Request;
use support\Response;

class UploadController
{
    /**
     * 图片上传接口，支持多文件
     * @param Request $request
     * @return Response
     */
    public function image(Request $request): Response
    {
        $files = $request->file('editormd-image-file');
        if (!$files) {
            // 兼容单文件上传
            $file = $request->file();
            $files = $file ? [$file[key($file)]] : [];
        } elseif (!is_array($files)) {
            $files = [$files];
        }
        $urls = [];
        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $ext = strtolower($file->getUploadExtension());
            $forbidden = ['php', 'php3', 'php5', 'css', 'js', 'html', 'htm', 'asp', 'jsp'];
            if (in_array($ext, $forbidden)) {
                continue;
            }
            $relative_dir = 'upload/img/' . date('Ymd');
            $base_dir = base_path() . '/plugin/acms/public/' . $relative_dir;
            if (!is_dir($base_dir)) {
                mkdir($base_dir, 0777, true);
            }
            $name = bin2hex(pack('Nn', time(), random_int(1, 65535))) . ".{$ext}";
            $full_path = $base_dir . '/' . $name;
            $file->move($full_path);
            $urls[] = "/app/acms/{$relative_dir}/{$name}";
        }
        if (empty($urls)) {
            return json([
                'success' => 0,
                'message' => '上传失败',
                'url' => ''
            ]);
        }
        // editor.md 只用第一个图片
        return json([
            'success' => 1,
            'message' => '上传成功',
            'url' => $urls[0],
            'urls' => $urls // 可扩展返回所有图片
        ]);
    }
}
