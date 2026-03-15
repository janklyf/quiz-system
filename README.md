# 📚 交互式答题系统

基于 MinerU 的自动试卷解析和交互式答题系统

## ✨ 功能特性

- ✅ **试卷上传**: 支持 PDF、Word 格式试卷上传
- ✅ **自动解析**: 使用 MinerU 自动解析试卷题目
- ✅ **智能识别**: 自动识别题目类型、选项和答案
- ✅ **交互答题**: 美观的答题界面，支持在线答题
- ✅ **成绩统计**: 实时统计答题成绩和正确率

## 🛠️ 技术栈

- **后端**: PHP 7.4+
- **前端**: HTML5, CSS3, JavaScript
- **UI 框架**: Bootstrap 5
- **PDF 转换**: MinerU
- **服务器**: Python HTTP Server

## 📦 安装步骤

### 1. 克隆仓库

```bash
git clone https://github.com/janklyf/quiz-system.git
cd quiz-system
```

### 2. 安装依赖

```bash
# 安装 MinerU
cd /home/xkxx/MinerU
python3 -m venv venv
source venv/bin/activate
pip install -e .
```

### 3. 配置服务器

```bash
# 启动 Web 服务器
cd public
python3 -m http.server 8888
```

### 4. 访问系统

打开浏览器访问: http://localhost:8888

## 📖 使用说明

### 上传试卷

1. 打开系统首页
2. 点击上传区域或拖拽 PDF/Word 文件
3. 等待系统自动解析

### 开始答题

1. 解析完成后自动进入答题界面
2. 点击选项选择答案
3. 提交答案查看成绩

### 查看成绩

- 系统自动批改选择题
- 显示正确答案和分数
- 可重新开始答题

## 📁 项目结构

```
quiz-system/
├── api/                 # 后端 API
│   └── upload.php       # 试卷上传和解析 API
├── public/              # 前端文件
│   ├── index.html       # 主页面
│   └── auto-test.html   # 自动测试页面
├── uploads/             # 上传文件目录
├── test_upload.php      # 测试脚本
└── README.md            # 项目说明
```

## 🎯 功能模块

### 1. 试卷上传模块
- 支持点击和拖拽上传
- 支持 PDF、Word 格式
- 实时进度提示

### 2. 题目解析模块
- 使用 MinerU 转换 PDF
- 自动识别题目类型
- 提取选项和答案

### 3. 答题模块
- 交互式选项选择
- 实时答题进度
- 答案验证

### 4. 成绩统计模块
- 自动批改
- 分数计算
- 错题分析

## 🚀 快速开始

```bash
# 1. 克隆项目
git clone https://github.com/janklyf/quiz-system.git
cd quiz-system

# 2. 启动服务器
cd public && python3 -m http.server 8888

# 3. 访问系统
# 浏览器打开 http://localhost:8888
```

## 📸 界面预览

![系统截图](https://github.com/janklyf/quiz-system/raw/main/screenshots/quiz-system.png)

## 🤝 贡献指南

欢迎提交 Issue 和 Pull Request！

## 📄 许可证

MIT License

## 👨‍💻 作者

janklyf

## 📧 联系方式

- GitHub: https://github.com/janklyf
- Email: lyf971@qq.com
