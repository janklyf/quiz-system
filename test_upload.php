<?php
// 测试脚本：模拟上传试卷
echo "<h2>📚 试卷解析测试</h2>";

$uploadDir = __DIR__ . '/uploads/';
$filename = '期末综合检测卷_B.pdf';
$filepath = $uploadDir . $filename;

if (file_exists($filepath)) {
    echo "<p><strong>试卷文件已找到：</strong> $filename</p>";

    // 使用 MinerU 转换
    $mineruPath = __DIR__ . '/../../MinerU/venv/bin/mineru';

    if (file_exists($mineruPath)) {
        echo "<p><strong>MinerU 路径：</strong> $mineruPath</p>";

        $outputDir = __DIR__ . '/uploads/' . uniqid();
        mkdir($outputDir, 0777, true);

        echo "<p><strong>输出目录：</strong> $outputDir</p>";

        // 调用 MinerU 进行转换
        $cmd = sprintf(
            '%s -p "%s" -o "%s" -m txt 2>&1',
            escapeshellarg($mineruPath),
            escapeshellarg($filepath),
            escapeshellarg($outputDir)
        );

        echo "<p><strong>执行命令：</strong></p>";
        echo "<pre>" . htmlspecialchars($cmd) . "</pre>";

        exec($cmd, $output, $returnCode);

        echo "<p><strong>执行结果代码：</strong> $returnCode</p>";

        if ($returnCode === 0) {
            echo "<p><strong>转换成功！</strong></p>";

            // 读取转换结果
            $markdownFile = $outputDir . '/document.md';
            if (file_exists($markdownFile)) {
                $markdown = file_get_contents($markdownFile);

                echo "<p><strong>转换后的 Markdown 内容：</strong></p>";
                echo "<pre style='max-height: 500px; overflow: auto;'>" . htmlspecialchars($markdown) . "</pre>";

                // 简单的题目解析
                $questions = parseQuestions($markdown);

                echo "<h3>解析结果：</h3>";
                echo "<p><strong>共解析出 " . count($questions) . " 道题目</strong></p>";

                foreach ($questions as $q) {
                    echo "<div style='border-left: 4px solid #667eea; padding: 10px; margin: 10px 0; background: #f8f9ff;'>";
                    echo "<strong>题目 " . $q['id'] . "：</strong> " . htmlspecialchars($q['title']) . "<br>";
                    echo "<strong>类型：</strong> " . $q['type'] . "<br>";
                    if ($q['options']) {
                        echo "<strong>选项：</strong><br>";
                        foreach ($q['options'] as $i => $opt) {
                            echo "  " . chr(65 + $i) . ". " . htmlspecialchars($opt) . "<br>";
                        }
                    }
                    if ($q['answer']) {
                        echo "<strong>正确答案：</strong> " . $q['answer'] . "<br>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p><strong style='color: red;'>未找到 Markdown 文件！</strong></p>";
                echo "<p><strong>输出目录内容：</strong></p>";
                echo "<pre>" . htmlspecialchars(print_r(scandir($outputDir), true)) . "</pre>";
            }
        } else {
            echo "<p><strong style='color: red;'>MinerU 执行失败！</strong></p>";
            echo "<p><strong>错误信息：</strong></p>";
            echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        }
    } else {
        echo "<p><strong style='color: red;'>MinerU 未安装！</strong></p>";
    }
} else {
    echo "<p><strong style='color: red;'>试卷文件不存在！</strong></p>";
    echo "<p><strong>可用的文件：</strong></p>";
    echo "<pre>" . htmlspecialchars(print_r(scandir($uploadDir), true)) . "</pre>";
}

// 简单的题目解析函数
function parseQuestions($markdown) {
    $questions = [];
    $lines = explode("\n", $markdown);
    $currentQuestion = null;
    $questionNum = 1;

    foreach ($lines as $line) {
        $line = trim($line);

        // 检测题目行
        if (preg_match('/^[一二三四五六七八九十]+、|^[\d]+、|^[\d]+\./', $line)) {
            if ($currentQuestion) {
                $questions[] = $currentQuestion;
            }

            $currentQuestion = [
                'id' => $questionNum++,
                'type' => 'unknown',
                'title' => $line,
                'options' => [],
                'answer' => null
            ];

            if (stripos($line, '选择') !== false) {
                $currentQuestion['type'] = 'choice';
            } elseif (stripos($line, '简答') !== false) {
                $currentQuestion['type'] = 'essay';
            }
        }
        // 检测选项
        elseif ($currentQuestion && preg_match('/^[A-Z]\)[\s\.]/', $line)) {
            $currentQuestion['options'][] = trim(str_replace(['A)', 'B)', 'C)', 'D)', 'E)', 'F)'], '', $line));
        }
        // 检测答案
        elseif ($currentQuestion && preg_match('/(答案|正确答案)[：:]\s*([A-Z])/', $line, $matches)) {
            $currentQuestion['answer'] = strtoupper($matches[2]);
        }
    }

    if ($currentQuestion) {
        $questions[] = $currentQuestion;
    }

    return $questions;
}
?>
