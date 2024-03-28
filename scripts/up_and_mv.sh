#!/bin/bash

# 确保uploaded目录存在
mkdir -p uploaded

# 确保logs目录存在
mkdir -p logs

# 使用find命令查找output目录下的所有文件
find output -type f -print0 | while IFS= read -r -d '' file; do
  # 获取文件名（不包含路径）
  filename=$(basename "$file")

  # 构造上传URL
  upload_url="https://icon.matlab.run/$filename"

  # 使用curl上传文件
  response=$(curl -s -X PUT --data-binary "@$file" "$upload_url")

  # 检查响应是否包含"successfully"
  if [[ $response == *"successfully"* ]]; then
    # 移动文件到uploaded目录
    mv "$file" uploaded/
    # 将响应追加到日志文件
    echo "Uploaded $filename successfully. Response: $response" | tee -a logs/upload.log
  else
    # 如果上传失败，记录错误信息
    echo "Failed to upload $filename. Response: $response" | tee -a logs/upload.log
  fi
done


# nohup ./up_and_mv.sh > logs/upload.log 2>&1 &