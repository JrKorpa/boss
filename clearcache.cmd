@echo off 
color 0a
title ������־����
echo ��� ��� ��� ��� ������� ��� ��� ��� ����
echo ��� ��� ��� ��� ������� ��� ��� ��� ����
echo ���                                              ���
echo.���       ������ʯ��������������---���        ���
echo.���                                              ���
echo.���       ����������־���棬���Ե�......         ���
echo.���                                              ���
echo ��� ��� ��� ��� ������� ��� ��� ��� ����
echo ��� ��� ��� ��� ������� ��� ��� ��� ����
echo.
echo. 

echo ����������־�ͱ��뻺�棬���Ժ�...... 
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
echo �������!
pause