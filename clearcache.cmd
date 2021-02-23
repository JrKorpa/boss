@echo off 
color 0a
title 清理日志缓存
echo ★☆ ★☆ ★☆ ★☆ ★☆★☆★☆ ★☆ ★☆ ★☆ ★☆★
echo ★☆ ★☆ ★☆ ★☆ ★☆★☆★☆ ★☆ ★☆ ★☆ ★☆★
echo ★☆                                              ☆★
echo.★☆       珂兰钻石技术部开发环境---杨福友        ☆★
echo.★☆                                              ☆★
echo.★☆       正在清理日志缓存，请稍等......         ☆★
echo.★☆                                              ☆★
echo ★☆ ★☆ ★☆ ★☆ ★☆★☆★☆ ★☆ ★☆ ★☆ ★☆★
echo ★☆ ★☆ ★☆ ★☆ ★☆★☆★☆ ★☆ ★☆ ★☆ ★☆★
echo.
echo. 

echo 正在清理日志和编译缓存，请稍候...... 
echo off
del frame\~runtime.php /F /S /Q
del frame\*.log /F /S /Q
cd apps

for /d %%i in (*) do (
	del %%i\logs\*.* /F /S /Q
	rd %%i\logs\api_logs
	del %%i\tmp\template_c\*.* /F /S /Q
)

echo.  
echo 清理完毕!
pause