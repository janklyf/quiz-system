<?php
// 试卷上传和解析 API
header('Content-Type: application/json');

$uploadDir = __DIR__ . '/../uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['file'] ?? null;
    
    if (!$file) {
        echo json_encode(['success' => false, 'message' => '未上传文件']);
        exit;
    }
    
    $filename = uniqid() . '_' . basename($file['name']);
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // 使用 MinerU 转换
        $mineruPath = __DIR__ . '/../../MinerU/venv/bin/mineru';
        
        if (file_exists($mineruPath)) {
            $outputDir = __DIR__ . '/../uploads/' . uniqid();
            mkdir($outputDir, 0777, true);
            
            // 调用 MinerU 进行转换
            $cmd = sprintf(
                '%s -p "%s" -o "%s" -m txt 2>&1',
                escapeshellarg($mineruPath),
                escapeshellarg($filepath),
                escapeshellarg($outputDir)
            );
            
            exec($cmd, $output, $returnCode);
            
            // 读取转换结果
            $markdownFile = $outputDir . '/document.md';
            if (file_exists($markdownFile)) {
                $markdown = file_get_contents($markdownFile);
                
                // 简单的题目解析
                $questions = parseQuestions($markdown);
                
                echo json_encode([
                    'success' => true,
                    'filename' => $filename,
                    'questions' => $questions,
                    'message' => '试卷解析成功'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'MinerU 转换失败'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'MinerU 未安装'
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => '文件上传失败']);
    }
} else {
    echo json_encode(['success' => false, 'message' => '无效的请求方法']);
}

// 简单的题目解析函数
function parseQuestions($markdown) {
    $questions = [];
    $lines = explode("\n", $markdown);
    
    $currentQuestion = null;
    $questionNum = 1;
    
    foreach ($lines as $line) {
        $line = trim($line);
        
        // 检测题目行 (如 "一、选择题" 或 "1." 或 "1、")
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
            
            // 判断题目类型
            if (stripos($line, '选择') !== false) {
                $currentQuestion['type'] = 'choice';
            } elseif (stripos($line, '简答') !== false) {
                $currentQuestion['type'] = 'essay';
            }
        }
        // 检测选项 (A. xxx 或 A) xxx)
        elseif ($currentQuestion && preg_match('/^[A-Z]\)[\s\.]/', $line)) {
            $currentQuestion['options'][] = trim(str_replace(['A)', 'B)', 'C)', 'D)', 'E)', 'F)'], '', $line));
        }
        // 检测答案行 (如 "答案: A" 或 "正确答案: A")
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
